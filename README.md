# Gesuch-Erweiterung
Das Plugin ermöglicht es Nutzern zusätzliche Informationen für ihre Gesuche einzutragen sowie Präfixe zu pflegen, ob das Gesuch reserviert, frei oder teilweise vergeben ist. Präfixe können auch über die Themenübersicht geändert werden. An dieser Stelle haben Teammitglieder auch die Möglichkeit einzutragen, ob die Gesuche schon im SG/CSB sind und die zu exportieren, damit ein editieren, um den Code zu erhalten, hinfällig ist.

## Funktionen
__allgemeine Funktionen__
* Eintragung von zusätzlichen Informationen (Avatar, Alter, Kategorie)
* Änderung des Präfixes über die Themenansicht
* Übersichtsseite aller Gesuche erreichbar über `/misc.php?action=wanted`

__Funktionen für Admins__
* Festlegung für Bereiche in welchen Gesuchinformationen angegeben werden
* Export eines Gesuches, um das Editieren zu ersparen
* automatisches einfügen der Shortfacts
* Eintragung wann die Gesuche in Werbeforen gestellt worden

## Voraussetzungen
* [Enhanced Account Switcher](http://doylecc.altervista.org/bb/downloads.php?dlid=26&cat=2) muss installiert sein 

## Template-Änderungen
__Neues globales Template:__
* `wanted_shortfacts`

__Neue Templates:__
* `wanted_export_button`
* `wanted_export_overview`
* `wanted_forumdisplay_thread`
* `wanted_forumdisplay_thread_prefix`
* `wanted_forumdisplay_thread_prefixOwner`
* `wanted_misc`
* `wanted_misc_bit`
* `wanted_misc_changePrefix`
* `wanted_misc_team`
* `wanted_newthread`
* `wanted_showthread`

__Veränderte Templates:__
* `showthread` (wird um die Variablen `$wanted`, `$wantedPrefix` und `$shortfacts_wanted` erweitert)
* `forumdisplay_thread` (wird um die Variablen `$wanted`, `$wantedPrefix` und `$shortfacts_wanted` erweitert)
* `newthread` (wird um die Variable `$wanted` erweitert)
* `editpost` (wird um die Variable `$wanted` erweitert)

## Vorschaubilder
__Ansicht in der Threadübersicht__
![wanted_forumdisplay](https://aheartforspinach.de/upload/plugins/wanted_forumdisplay.png)

__Ansicht Präfix ändern__
![wanted_change_prefix](https://aheartforspinach.de/upload/plugins/wanted_changestat.png)

__Ansicht Einstellungsdatum ändern__
![wanted_change_date](https://aheartforspinach.de/upload/plugins/wanted_changedate.png)

__Gesuch exportieren__
![wanted_change_export](https://aheartforspinach.de/upload/plugins/wanted_export.png)

__Ansicht in Threadansicht__
![showthread](https://aheartforspinach.de/upload/plugins/wanted_showthread.png)

__Ansicht bei neuem Thema/Editieren__
![newthread](https://aheartforspinach.de/upload/plugins/wanted_newthread.png)
