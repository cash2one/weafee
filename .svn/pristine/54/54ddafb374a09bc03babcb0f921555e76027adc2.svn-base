#!/bin/sh
function count()
  for f in db/*.db; do
    echo $f
    sqlite3 $f < count.sql
  done

function main()
  for i in {1..100}; do
    sleep 600
    count > $i.txt
  done

main
