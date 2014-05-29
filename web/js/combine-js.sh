#!/bin/sh

src_files="app/global.js app/ajax-page.js"
src_dir="app"
dst_file="app.js"


function filemtime() 
{
    stat -c %Y "$1" | awk '{printf $1 "\n"}'
}

while true
do
    src_time=`filemtime "$src_dir/."`
    dst_time=`filemtime "$dst_file"`
    
    if [ $src_time -gt $dst_time ]
    then
        echo "File changed $src_time"
        echo "" > $dst_file
        #uglifyjs --source-map "$dst_file.map" -o "$dst_file" $src_files
        closure --create_source_map "$dst_file.map" --js_output_file "$dst_file" $src_files && echo "//# sourceMappingURL=app.js.map" >> "$dst_file"
    fi
    sleep 1
done
