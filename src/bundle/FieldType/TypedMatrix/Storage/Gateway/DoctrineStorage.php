<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\TypedMatrix\Storage\Gateway;

use eZ\Publish\SPI\FieldType\StorageGateway;

class DoctrineStorage extends StorageGateway
{
/*
ad_typedmatrix_definition
  contentclass_attribute_id
  contentclass_version
  column_index
  column_identifier
  column_names
  fieldtype
  definition…

ad_typedmatrix_value
  contentobject_attribute_id
  row_index
  column_index
  data
 */
}