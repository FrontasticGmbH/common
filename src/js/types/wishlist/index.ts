// This file is autogenerated – run `ant apidocs` to update it

import {
    ApiDataObject as CoreApiDataObject,
} from '../core/'

export interface LineItem extends CoreApiDataObject {
     lineItemId: string;
     name: string;
     type: string;
     addedAt: any /* \DateTimeImmutable */;
     count: number;
     dangerousInnerItem?: any;
}

export interface Wishlist extends CoreApiDataObject {
     wishlistId: string;
     wishlistVersion?: string;
     anonymousId?: string;
     accountId?: string;
     name: string[];
     lineItems: LineItem[];
     dangerousInnerWishlist?: any;
}
