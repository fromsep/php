#!/bin/bash

today='v'`date +%y%m%d`

allTagString=`git tag`

OLD_IFS="$IFS"
IFS=" "
arr=($allTagString)
IFS=$OLD_IFS

number=0
isExist=0
prefix=""
code=0
for i in $arr
do 
    prefix=${i:0:7}
    code=${i:8}
    
    if [ "$prefix" == "$today" ]
    then
        if [ "$code" == "" ]
	then
	    number=1
	else
	    if [ $code > $number ]
	    then
	       code=$number
	       let "number = code + 1"
	    fi
	fi
    fi
done

next_tag=$today

if [ $number != 0 ]
then
    next_tag="$today.$number"
fi

function update()
{
    `git checkout master && git pull`

    if [ $? == 0 ]
    then
        echo "branch master is pulled"
    else
        exit
    fi
}

#update


function addTag()
{
    `git tag $next_tag`

    if [ $? == 0 ]
    then
        echo "$next_tag added success"
    else
        exit
    fi
}

addTag


function pushTag()
{
    `git push origin [$next_tag]`
    if [ $? == 0 ]
    then
        echo "tag $next_tag pushed success"
    else
        echo "tag $next_tag pushed failed"
    fi
}

#pushTag

