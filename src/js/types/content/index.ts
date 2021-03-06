// This file is autogenerated – run `ant apidocs` to update it

import {
    ApiDataObject as CoreApiDataObject,
} from '../core/'

export interface AttributeFilter extends CoreApiDataObject {
     name: string;
     value: string;
}

export interface ContentType extends CoreApiDataObject {
     contentTypeId: string;
     name: string;
}

export interface Query extends CoreApiDataObject {
     contentType?: string;
     query?: string;
     contentIds?: any;
     attributes?: AttributeFilter[];
}

export interface Result extends CoreApiDataObject {
     offset: number;
     total: number;
     count: number;
     items: any;
}
