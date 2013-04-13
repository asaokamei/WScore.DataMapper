CSV Definition for Model
========================

Sections
--------

A CSV for Model is consisted of 4 sections: properties, selector, validation, and relation.

Each section are denoted by #. 

Comments maybe denoted by # but not with the section name as shown above. 

properties
--------------

*    defines how to interact with database. 

*    id: column name. must be the same as in entity. 

*    active: active flag, but not used. 

*    label: column name for humans, such as 'task to do'. 

*    dbDef: database definition, such as 'char(1)'. but not really used. 

*    notNull: NOT NULL in db. not sure how it is used...

*    default: a default value if not set. 

*    required: required to have some value or not. 

*    extra: either of:
     -    updatedAt
     -    createdAt
     -    primaryKey (not supported yet). 

*    bindType: how to bind in PDO: 'string' or 'number'. 

selector
--------------

*    id: column name. must be the same as in entity.

*    selector: 

*    type: type of selector, such as 'text', 'radio', etc.

*    choice: list of possible values for radio button, select, or even for text. example: '[m:male,f:female]'.   

*    extra: specify classes, etc. example: 'ime:on | placeholder:task memo',

validation
--------------

*    id: column name. must be the same as in entity.

*    type: type of validation such as 'text', 'date', etc.

*    pattern: regular expression to match with. 

*    extra: specify other rules. example: 'string:hankaku'.

*    required in property: used to determine required property or not. 

relation
--------------

*    id: column name. must be the same as in entity.

*    type: type of relation: HasOne, Joined, or JoinBy. 

*    entity: class name of related entity. example: '\Contacts\Entity\Contact'.

*    by: class name of join model for JoinBy. example: '\Contacts\Entity\Fr2tg'.

*    source: column name used in source entity. primary key is used if omitted. 

*    target: column name used in target entity. primary key is used if omitted.

*    bySource: column name used in join-model to join with source entity. 

*    byTarget: column name used in join-model to join with target entity.


Example of CSV
--------------

```CSV
#properties,,,,,,,,
"id","active","label","dbDef","notNull","default","required","extra","bindType"
"friend_id",,"friend code","serial","TRUE",,,,
"friend_name",,"name","text","TRUE","''","TRUE",,
"gender",,"gender type","Char(1)",,"''","TRUE",,
"birthday",,"birthday","date",,"NULL","FALSE",,

#selector,,,,,,,,
"id","selector","type","choice","extra",,,,
"friend_id",,,,,,,,
"friend_name","Selector","text",,"ime:on",,,,
"gender","Selector","radio","[m:male,f:female]",,,,,
"birthday","Selector","Date",,"ime:off",,,,

#validation,,,,,,,,
"id","type","extra","pattern",,,,,
"friend_id","number",,,,,,,
"friend_name","text",,,,,,,
"gender","code",,,,,,,
"birthday","date",,,,,,,

#relation,,,,,,,,
"id","type","source","target","entity","by","bySource","byTarget",
"contacts","joined",,,"\App\Contacts\Entity\Contact",,,,
"tags","joinBy",,,"\App\Contacts\Entity\Tag","\App\Contacts\Entity\Fr2tg",,,
```

