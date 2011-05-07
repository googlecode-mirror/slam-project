#!/bin/sh

if [ ! -e "/System/Library/Printers/Libraries/convert" ]
then
	echo "Could not find CUPS convert utility. This could be because this is not a MacOS computer, or because it was installed incorrectly."
fi
