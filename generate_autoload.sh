echo "<?php" > ext_autoload.php
echo "\$extPath = \\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility::extPath('qbevents');" >> ext_autoload.php
echo 'return array(' >> ext_autoload.php

find Resources/Private/PHP/simshaun/recurr/src/  -name '*.php' | while read file
do
	echo $file | sed -e 's/.*/&\n&/' -e 's:.*/src/::' -e 's/.php//' -e 's:/:\\\\:g' -e "s#.*#    '\\0'=> \$extPath . '$file' ,#"  >> ext_autoload.php
done

find Resources/Private/PHP/doctrine/collections/lib/ -name '*.php' | while read file
do
	echo $file | sed -e 's/.*/&\n&/' -e 's:.*/lib/::' -e 's/.php//' -e 's:/:\\\\:g' -e "s#.*#    '\\0' => \$extPath . '$file',#"  >> ext_autoload.php
done

echo ");" >> ext_autoload.php
