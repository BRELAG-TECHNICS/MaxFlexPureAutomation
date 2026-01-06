# MaxFlex Pure Automation
Benötigt das Knockaut Alarmanlagemodul als übergeordnete Instanz.

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Einrichten der Instanz](#3-einrichten-der-instanz)
4. [Statusvariablen und Profile](#4-statusvariablen-und-profile)

### 1. Funktionsumfang

- Zur Nutzung eines MaxFlex als "unabhängiger" Steuereinheit. Beim MaxFlex muss auf allen Tasten als Funktion None und Kanal 1 eingestellt sein.
- Die LEDs der jeweiligen Taste, können gesteuert werden.

### 2. Voraussetzungen

- IP-Symcon ab Version 5.0

### 3. Einrichten der Instanz

__Konfigurationsseite__:

Name                                 | Beschreibung
------------------------------------ | ---------------------------------
ID                                   | Auswahl der eingerichteten ID (Speicherpunkt bei der eGate)
Switch: "MaxFlex als Taster nutzen?" | Wenn true, wird das schaltverhalten auf Taster umgestellt.

### 4. Statusvariablen und Profile

- Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen. 
- Profile werden keine benötigt.

**Taster-Modus:**

Name        | Typ     | Beschreibung
------------| ------- | -----------
Pushbutton  | Integer | Zeigt für die dauer des drücken, die Tastennummer an. Beim loslassen, der Taste hat die Variable den Wert 0.

Tasten sind, beginnend von der obersten Reihe, immer von links nach rechts 1 - 8 durchnummeriert.

**Schalter-Modus:**

Name            | Typ     | Beschreibung
----------------| ------- | -----------
Schalter 1 - 8  | Boolean | Beim betätigen der jeweiligen Taste, wird zwischen true und false gewechselt.

Tasten sind, beginnend von der obersten Reihe, immer von links nach rechts 1 - 8 durchnummeriert.

### 5. Funktionen

BRELAG_SendCommand()

```php
BRELAG_SendCommand(integer $InstanzID, integer $Instruction integer $Command, integer $Value, integer $Priority);
```
Sendet den Wert für die Instruction (richtung) $Instruction, das Kommando $Command mit dem Wert $Value und der Priorität $Priority. Die Funktion liefert keinerlei Rückgabewert.
```php
// Beispiel:
BRELAG_SendCommand(12345, 1, 0);
```

BRELAG_SwitchLED()
```php
BRELAG_SwitchLED(integer $LEDnumber, integer $State);
```
Sendet den Befehl an die LED der jeweiligen Taste $LEDnumber den Statusbefehl $State.
- 0 = Aus
- 1 = Leuchten
- 2 = Blinken

Die Funktion liefert keinerlei Rückgabewert.

BRELAG_changeButtonValue();
```php
BRELAG_changeButtonValue(integer $buttonNumber);
```
Diese Funktion hat keinerlei auswirkungen auf den MaxFlex selbts. Es wird kein Funkbefehl abgesetzt.

**Im Schalter-Modus:**

Schaltet die jeweilige Schalter variable $buttonNumber um (true / false). Die Funktion liefert keinerlei Rückgabewert.

**Im Taster-Modus:**

Setzt den Wert $buttonNumber in die Variable "PushButton". Die Funktion liefert keinerlei Rückgabewert.
