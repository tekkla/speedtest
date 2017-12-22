#!/bin/bash
# -------------------------------------------------------------------
# Skript zum automatisierten durchfuehren von Tests auf speedtest.net
# -------------------------------------------------------------------
#
# Voraussetung: 
# Es muss das speedtest-cli Paket installiert sein
# Fuer den Upload auf einen anderen Server wird SCP verwendet
#
#
# Dieses Skript ausfuehrbar machen und dann bei Bedarf starten.
# Ideal ist es einen Cronjob dafuer anzulegen.
#
# Bei Bedarf den Upload auf einen anderen Server per SCP am Ende des
# Skriptes einfach auskommentieren.
# -------------------------------------------------------------------

# Array mit ServerIDs zum Abfragen
# IDs kann man auf speedtest.net dem Quellcode entnehmen
server=(1746 4556 4886 4087 6219 4617 3628)

# Lokaler Speicherort für die JSON Dateien
target="~/speedtest"

# Optional: Wenn Logs per SCP auf anderen Rechner uebertragen werden sollen

# SSH Port
port=<port>

# Username
user="<username>"

# Name oder IP des Zielrechners
host="<hostname>"

# Pfad auf dem Zielrechner, in den die Logs abgelegt werden sollen
# Sollte ein Verzeichnis mit Namen /logs im gleichen Verzeichnis wie die PHP Dateien sein
# Ein anderes Verzeichnis muss im auswertenden PHP Script angepasst werden
remotepath="/dein/entferntes/verzeichnis/logs"

# Funktion fuer Timestamp
timestamp() {
  date +%s
}

# Jede Messung bekommt ihr eigenes Verzeichnis
logdir="$target/$(timestamp)"

mkdir $logdir

# Dies die Schleife für die jeweiligen Speedtests
for item in ${server[*]}
do
        logfile="$logdir/$item.json"
        speedtest-cli --json --server $item >> $logfile
done

# Hier findet der Upload des Verzeichnisses auf einen Remoteserver per scp statt
scp -prq -P $port $logdir $user@$host:$remotepath
