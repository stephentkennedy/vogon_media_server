<?php
shell_exec($_SESSION['minidlna'].' -f '.$_SESSION['minidlna_dir']. DIRECTORY_SEPARATOR . 'minidlna.conf -P '.$_SESSION['minidlna_dir']. DIRECTORY_SEPARATOR . 'minidlna.pid');