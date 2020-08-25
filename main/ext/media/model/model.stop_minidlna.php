<?php
shell_exec('xargs kill <'.$_SESSION['minidlna_dir']. DIRECTORY_SEPARATOR .'minidlna.pid');