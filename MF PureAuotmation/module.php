<?php

class MaxFlexPureAutomation extends IPSModule {

	const LED_OFF = 0;
	const LED_ON = 1;
	const LED_BLINK = 2;

	public function Create(){
		//Never delete this line!
		parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
		//You cannot use variables here. Just static values.

		$this->ConnectParent("{1252F612-CF3F-4995-A152-DA7BE31D4154}"); //DominoSwiss eGate

		if(!IPS_VariableProfileExists("BRELAG.Switch")) {
			IPS_CreateVariableProfile("BRELAG.Switch", 0);
			IPS_SetVariableProfileIcon("BRELAG.Switch", "Power");
			IPS_SetVariableProfileAssociation("BRELAG.Switch", 0, $this->Translate("Off"), "", -1);
			IPS_SetVariableProfileAssociation("BRELAG.Switch", 1, $this->Translate("On"), "", -1);
		}

		$this->RegisterPropertyInteger("ID", 1);
		$this->RegisterPropertyBoolean("LED1", false); // If true turns the LED on after an action
		//$this->RegisterPropertyBoolean("SWITCHBUTTON1", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED2", false); // If true turns the LED on after an action
		//$this->RegisterPropertyBoolean("SWITCHBUTTON2", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED3", false); // If true turns the LED on after an action
		//$this->RegisterPropertyBoolean("SWITCHBUTTON3", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED4", false); // If true turns the LED on after an action
		//$this->RegisterPropertyBoolean("SWITCHBUTTON4", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED5", false); // If true turns the LED on after an action
		//$this->RegisterPropertyBoolean("SWITCHBUTTON5", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED6", false); // If true turns the LED on after an action
		//$this->RegisterPropertyBoolean("SWITCHBUTTON6", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED7", false); // If true turns the LED on after an action
		//$this->RegisterPropertyBoolean("SWITCHBUTTON7", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED8", false); // If true turns the LED on after an action
		//$this->RegisterPropertyBoolean("SWITCHBUTTON8", false); // false = Button, true = switch
		
		$this->RegisterVariableBoolean("BUTTON1", $this->Translate("Button1"), "BRELAG.Switch", 1);
		$this->RegisterVariableBoolean("BUTTON2", $this->Translate("Button2"), "BRELAG.Switch", 2);
		$this->RegisterVariableBoolean("BUTTON3", $this->Translate("Button3"), "BRELAG.Switch", 3);
		$this->RegisterVariableBoolean("BUTTON4", $this->Translate("Button4"), "BRELAG.Switch", 4);
		$this->RegisterVariableBoolean("BUTTON5", $this->Translate("Button5"), "BRELAG.Switch", 5);
		$this->RegisterVariableBoolean("BUTTON6", $this->Translate("Button6"), "BRELAG.Switch", 6);
		$this->RegisterVariableBoolean("BUTTON7", $this->Translate("Button7"), "BRELAG.Switch", 7);
		$this->RegisterVariableBoolean("BUTTON8", $this->Translate("Button8"), "BRELAG.Switch", 8);

	}

	public function Destroy() {
		//Never delete this line!
		parent::Destroy();
		
	}
	
	public function ApplyChanges() {
		//Never delete this line!
		parent::ApplyChanges();
		
	}


	public function ReceiveData($JSONString) {

		$data = json_decode($JSONString);
		
		$this->SendDebug("BufferIn", print_r($data->Values, true), 0);
		$id = $data->Values->ID;
		$command = $data->Values->Command;

		if($id == $this->ReadPropertyInteger("ID")) {

			$value = $data->Values->Value;

			if($command == 42) {
					switch($value) {
						case 1: // Button 1
								$this->changeButtonValue(1);
						break;
	
						case 2: // Button 2
								$this->changeButtonValue(2);
						break;
	
						case 4: // Button 3
								$this->changeButtonValue(3);
						break;
	
						case 8: // Button 4
								$this->changeButtonValue(4);
						break;
	
						case 16: // Button 5
								$this->changeButtonValue(5);
						break;
	
						case 32: // Button 6
								$this->changeButtonValue(6);
						break;
	
						case 64: // Button 7
								$this->changeButtonValue(7);
						break;
	
						case 128: // Button 8
								$this->changeButtonValue(8);
						break;
					}
			}
		}
	}

	public function changeButtonValue($buttonNumber) {
		$switch_button = "SWITCHBUTTON" . $buttonNumber;
		$button = "BUTTON" . $buttonNumber;
		$LEDlight = "LED" . $buttonNumber;

		if(GetValue($this->GetIDForIdent($button))) {
			SetValue($this->GetIDForIdent($button), false);
			//if(GetValue($this->ReadPropertyBoolean($LEDlight))) {
				$this->SwitchLED($buttonNumber, self::LED_OFF);
			//} 
		} else {
			SetValue($this->GetIDForIdent($button), true);
			//if(GetValue($this->ReadPropertyBoolean($LEDlight))) {
				$this->SwitchLED($buttonNumber, self::LED_ON);
			//} 
		}
	}

	private function SwitchLED(int $LEDnumber, int $State) {
		$this->SetLED($LEDnumber -1 + $State * 8);
	}

	private function SetLED(int $Value){
		$this->SendCommand(1, 43, $Value, 3);
	}

	public function SendCommand(int $Instruction, int $Command, int $Value, int $Priority) {
		// CheckNr 2942145
		$id = $this->ReadPropertyInteger("ID");
		return $this->SendDataToParent(json_encode(Array("DataID" => "{C24CDA30-82EE-46E2-BAA0-13A088ACB5DB}", "Instruction" => $Instruction, "ID" => $id, "Command" => $Command, "Value" => $Value, "Priority" => $Priority)));
	}

	private function RegisterSecurityMode(int $ID) {
		$this->RegisterMessage($ID, 10603 /* VM_UPDATE */);
	}
}

?>