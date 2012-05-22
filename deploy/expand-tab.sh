# tmpl, inc, css.js,php 
for i in $(find -type f -iname "*.tmpl"); do expand -t4 $i > $i.bkp; mv $i.bkp $i ; done
for i in $(find -type f -iname "*.inc"); do expand -t4 $i > $i.bkp; mv $i.bkp $i ; done
for i in $(find -type f -iname "*.css"); do expand -t4 $i > $i.bkp; mv $i.bkp $i ; done
for i in $(find -type f -iname "*.js"); do expand -t4 $i > $i.bkp; mv $i.bkp $i ; done
for i in $(find -type f -iname "*.php"); do expand -t4 $i > $i.bkp; mv $i.bkp $i ; done
