<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\TypedMatrix\Storage\Gateway;

use eZ\Publish\SPI\FieldType\StorageGateway;

class DoctrineStorage extends StorageGateway
{
/*

adtypedmatrix_contentclass_columns
| id                          | int(11)      | NO   | PRI | NULL    | auto_increment |
| version                     | int(11)      | NO   | PRI | 0       |                |
| can_translate               | int(11)      | YES  |     | 1       |                |
| category                    | varchar(25)  | NO   |     |         |                |
| contentclass_id             | int(11)      | NO   | MUL | 0       |                |
| contentclassattribute_id    | int(11)      | NO   | MUL | 0       |                |
| contentclasscolumn_index    | int(11)      | NO   | MUL | 0       |                |
| data_float1                 | double       | YES  |     | NULL    |                |
| data_float2                 | double       | YES  |     | NULL    |                |
| data_float3                 | double       | YES  |     | NULL    |                |
| data_float4                 | double       | YES  |     | NULL    |                |
| data_int1                   | int(11)      | YES  |     | NULL    |                |
| data_int2                   | int(11)      | YES  |     | NULL    |                |
| data_int3                   | int(11)      | YES  |     | NULL    |                |
| data_int4                   | int(11)      | YES  |     | NULL    |                |
| data_text1                  | varchar(255) | YES  |     | NULL    |                |
| data_text2                  | varchar(50)  | YES  |     | NULL    |                |
| data_text3                  | varchar(50)  | YES  |     | NULL    |                |
| data_text4                  | varchar(255) | YES  |     | NULL    |                |
| data_text5                  | longtext     | YES  |     | NULL    |                |
| data_type_string            | varchar(50)  | NO   |     |         |                |
| identifier                  | varchar(50)  | NO   |     |         |                |
| is_information_collector    | int(11)      | NO   |     | 0       |                |
| is_required                 | int(11)      | NO   |     | 0       |                |
| is_searchable               | int(11)      | NO   |     | 0       |                |
| is_thumbnail                | tinyint(1)   | NO   |     | 0       |                |
| placement                   | int(11)      | NO   |     | 0       |                |
| serialized_data_text        | longtext     | YES  |     | NULL    |                |
| serialized_description_list | longtext     | YES  |     | NULL    |                |
| serialized_name_list        | longtext     | NO   |     | NULL    |                |


adtypedmatrix_contentobject_cell
| id                       | int(11)      | NO   | PRI | NULL    | auto_increment |
| version                  | int(11)      | NO   | PRI | 0       |                |
| contentclassattribute_id | int(11)      | NO   | MUL | 0       |                |
| contentobjectattribute_id| int(11)      | NO   | MUL | 0       |                |
| contentclasscolumn_id    | int(11)      | NO   | MUL | 0       |                |
| contentobjectrow_id      | int(11)      | NO   | MUL | 0       |                |
| data_float               | double       | YES  |     | NULL    |                |
| data_int                 | int(11)      | YES  |     | NULL    |                |
| data_text                | longtext     | YES  |     | NULL    |                |
| data_type_string         | varchar(50)  | YES  |     |         |                |
| language_code            | varchar(20)  | NO   | MUL |         |                |
| language_id              | bigint(20)   | NO   |     | 0       |                |
| sort_key_int             | int(11)      | NO   | MUL | 0       |                |
| sort_key_string          | varchar(255) | NO   | MUL |         |                |

 */
}