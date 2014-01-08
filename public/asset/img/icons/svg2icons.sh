#/bin/bash

dimensions="14 24 32"
files=`ls *.svg | sed -s 's/\.svg//g'`

for f in $files; do
    for d in $dimensions; do
        inkscape -z -y 0 -e $f-$d.png -w $d -h $d $f.svg
    done
done
