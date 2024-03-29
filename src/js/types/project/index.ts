// This file is autogenerated – run `ant apidocs` to update it

import {
    ApiDataObject as CoreApiDataObject,
} from '../core/'

export interface Attribute extends CoreApiDataObject {
     attributeId: string;
     /**
      * TYPE_*
      */
     type: string;
     /**
      * The labels with the locale as key and the actual label as value. `null`
      * if the label is unknown
      */
     label?: Record<string, string> | [] | null;
     values?: null | any;
}
