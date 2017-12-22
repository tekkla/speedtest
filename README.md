# speedtest
Eine simple Auswertung von Logs im JSON Format über mehrere Server bei speedtest.net

Diese Auswertung dient mir persönlich zur Dokumentation meiner aktuellen Probleme am Kabelinternetanschluss von Vodafone. Diese Auswertung berücksichtigt in aktueller Form nur den Downloadspeed. Ich habe zwar schon die rudimentären Grundlagen für eine Uploadspeed-Auswertung in den Speedtest-Klassen erstellt, greife dessen Daten bei der Auswertung aber nicht als Bewertungskriterium ab. 

Wer den Upload als Basis für die Auswertung braucht, der muss die Auswertungsdarstellung selber ändern. 

## speedtest.sh 
Diese Datei enthält das Skript mit den auszuführenden Speedtests. Einfach als ausführbar markieren und dann per cronjob laufen lassen. Die möglichen Parameter sind in der Datei kommentiert.

## Performance
Aktuell werden die einzelnen JSON Dateien in einem Stück geladen. In der Folge wird die Auswertung mit einer ansteigenden Menge von Logs immer langsamer. Es würde Sinn machen die Auswertungen per Ajax erst beim Klicken auf den jeweiligen Tag zu laden. Vielleicht mache ich das ja noch. Es steht natürlich jedem frei da sein eigenes Ding draus zu stricken. :)
