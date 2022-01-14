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
		$this->RegisterVariableInteger("MFTYPE", 46);
		$this->RegisterPropertyBoolean("LED-1", false); // If true turns the LED on after an action
		$this->RegisterPropertyBoolean("SWITCH-BUTTON-1", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED-2", false); // If true turns the LED on after an action
		$this->RegisterPropertyBoolean("SWITCH-BUTTON-2", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED-3", false); // If true turns the LED on after an action
		$this->RegisterPropertyBoolean("SWITCH-BUTTON-3", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED-4", false); // If true turns the LED on after an action
		$this->RegisterPropertyBoolean("SWITCH-BUTTON-4", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED-5", false); // If true turns the LED on after an action
		$this->RegisterPropertyBoolean("SWITCH-BUTTON-5", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED-6", false); // If true turns the LED on after an action
		$this->RegisterPropertyBoolean("SWITCH-BUTTON-6", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED-7", false); // If true turns the LED on after an action
		$this->RegisterPropertyBoolean("SWITCH-BUTTON-7", false); // false = Button, true = switch
		$this->RegisterPropertyBoolean("LED-8", false); // If true turns the LED on after an action
		$this->RegisterPropertyBoolean("SWITCH-BUTTON-8", false); // false = Button, true = switch
		
		$this->RegisterVariableBoolean("BUTTON-1", $this->Translate("Button-1"), "BRELAG.Switch", 1);
		$this->RegisterVariableBoolean("BUTTON-1", $this->Translate("Button-2"), "BRELAG.Switch", 2);
		$this->RegisterVariableBoolean("BUTTON-1", $this->Translate("Button-3"), "BRELAG.Switch", 3);
		$this->RegisterVariableBoolean("BUTTON-1", $this->Translate("Button-4"), "BRELAG.Switch", 4);
		$this->RegisterVariableBoolean("BUTTON-1", $this->Translate("Button-5"), "BRELAG.Switch", 5);
		$this->RegisterVariableBoolean("BUTTON-1", $this->Translate("Button-6"), "BRELAG.Switch", 6);
		$this->RegisterVariableBoolean("BUTTON-1", $this->Translate("Button-7"), "BRELAG.Switch", 7);
		$this->RegisterVariableBoolean("BUTTON-1", $this->Translate("Button-8"), "BRELAG.Switch", 8);

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
							$this->changeButtonValue(1, $value);
						break;
	
						case 2: // Button 2
							$this->changeButtonValue(2, $value);
						break;
	
						case 4: // Button 3
							$this->changeButtonValue(3, $value);
						break;
	
						case 8: // Button 4
							$this->changeButtonValue(4, $value);
						break;
	
						case 16: // Button 5
							$this->changeButtonValue(5, $value);
						break;
	
						case 32: // Button 6
							$this->changeButtonValue(6, $value);
						break;
	
						case 64: // Button 7
							$this->changeButtonValue(7, $value);
						break;
	
						case 128: // Button 8
							$this->changeButtonValue(8, $value);
						break;
					}
			}
		}
	}

	public function changeButtonValue($button, $value) {
		$switch_button = "SWITCH-BUTTON-" . $button;
		$button = "BUTTON-" . $button;
		$LEDlight = "LED-" . $button;
			if($this->ReadPropertyInteger($switch_button)) {
				if(GetValue($this->GetIDForIdent($button))) {
					SetValue($this->GetIDForIdent($button), false);
					if($this->ReadPropertyInteger($LEDlight)) {
						$this->SwitchLED($button, self::LED_OFF);
					}
				} else {
					SetValue($this->GetIDForIdent($button), true);
					if($this->ReadPropertyInteger($LEDlight)) {
						$this->SwitchLED($button, self::LED_ON);
					}
				}
			} else {
				if($value >= 0) {
					SetValue($this->GetIDForIdent($button), true);
					if($this->ReadPropertyInteger($LEDlight)) {
						$this->SwitchLED($button, self::LED_ON);
					}
				} else {
					SetValue($this->GetIDForIdent($button), false);
					if($this->ReadPropertyInteger($LEDlight)) {
						$this->SwitchLED($button, self::LED_OFF);
					}
				}
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