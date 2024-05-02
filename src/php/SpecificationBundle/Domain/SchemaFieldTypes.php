<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class SchemaFieldTypes
{
    /**
     * This is all the supported schema field types.
     */
    public const SCHEMA_TYPE_ENUM = "enum";
    public const SCHEMA_TYPE_DYNAMIC_FILTER = "dynamic-filter";
    public const SCHEMA_TYPE_BOOLEAN = "boolean";
    public const SCHEMA_TYPE_CUSTOM = "custom";
    public const SCHEMA_TYPE_DATA_SOURCE = "dataSource";
    public const SCHEMA_TYPE_DECIMAL = "decimal";
    public const SCHEMA_TYPE_INT = "integer";
    public const SCHEMA_TYPE_JSON = "json";
    public const SCHEMA_TYPE_MARKDOWN = "markdown";
    public const SCHEMA_TYPE_MEDIA = "media";
    public const SCHEMA_TYPE_NODE = "node";
    public const SCHEMA_TYPE_NUMBER = "number";
    public const SCHEMA_TYPE_REFERENCE = "reference";
    public const SCHEMA_TYPE_STREAM = "stream";
    public const SCHEMA_TYPE_STRING = "string";
    public const SCHEMA_TYPE_TEXT = "text";
    public const SCHEMA_TYPE_TREE = "tree";
    public const SCHEMA_TYPE_INSTANT = "instant";
    public const SCHEMA_TYPE_GROUP = "group";
    public const SCHEMA_TYPE_IMAGE = "image";
    public const SCHEMA_TYPE_DESCRIPTION = "description";
    public const SCHEMA_TYPE_TASTIC = "tastic";
    public const SCHEMA_TYPE_ENCRYPTED = "encrypted";
}
