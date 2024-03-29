// This file is autogenerated – run `ant apidocs` to update it

import {
    ApiDataObject as CoreApiDataObject,
} from '../core/'

export interface Category extends CoreApiDataObject {
     categoryId: string;
     name: string;
     depth: number;
     /**
      * The materialized id path for this category.
      */
     path: string;
     slug: string;
     /**
      * Access original object from backend
      *
      * This should only be used if you need very specific features
      * right NOW. Please notify Frontastic about your need so that
      * we can integrate those twith the common API. Any usage off
      * this property might make your code unstable against future
      * changes.
      */
     dangerousInnerCategory?: any;
}

/**
 * Class Product
 */
export interface Product extends CoreApiDataObject {
     productId: string;
     /**
      * The date and time when this product was last changed or `null` if the
      * date is unknown.
      *
      * // TODO: Do we want to start typing properties now that we're on 7.4?
      */
     changed?: null | any /* \DateTimeImmutable */;
     version?: null | string;
     name: string;
     slug: string;
     description?: string;
     categories?: string[];
     variants: Variant[];
     /**
      * Access original object from backend
      *
      * This should only be used if you need very specific features
      * right NOW. Please notify Frontastic about your need so that
      * we can integrate those twith the common API. Any usage off
      * this property might make your code unstable against future
      * changes.
      *
      * Should only be accessed in lifecycle event listeners,
      * and not in controllers, because ProductApiWithoutInner removes
      * this value before the product is returned to a controller.
      */
     dangerousInnerProduct?: any;
}

export interface ProductType extends CoreApiDataObject {
     productTypeId: string;
     name: string;
     /**
      * Access original object from backend
      *
      * This should only be used if you need very specific features
      * right NOW. Please notify Frontastic about your need so that
      * we can integrate those twith the common API. Any usage off
      * this property might make your code unstable against future
      * changes.
      */
     dangerousInnerProductType?: any;
}

export interface Variant extends CoreApiDataObject {
     id: string;
     sku: string;
     groupId?: string;
     /**
      * The product price in cent
      */
     price: number;
     /**
      * If a discount is applied to the product, this contains the reduced value.
      */
     discountedPrice?: null | number;
     /**
      * Array of discount descriptions
      */
     discounts?: any;
     /**
      * A three letter currency code in upper case.
      */
     currency?: string;
     attributes?: any;
     images?: any;
     isOnStock?: boolean;
     /**
      * Access original object from backend
      *
      * This should only be used if you need very specific features
      * right NOW. Please notify Frontastic about your need so that
      * we can integrate those twith the common API. Any usage off
      * this property might make your code unstable against future
      * changes.
      */
     dangerousInnerVariant?: any;
}
