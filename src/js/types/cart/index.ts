// This file is autogenerated – run `ant apidocs` to update it

import {
    ApiDataObject as CoreApiDataObject,
} from '../core/'

import {
    Address as AccountAddress,
} from '../account/'

export interface Cart extends CoreApiDataObject {
     cartId: string;
     cartVersion?: string;
     lineItems: LineItem[];
     email?: string;
     birthday?: any /* \DateTimeImmutable */;
     shippingInfo?: null | ShippingInfo;
     shippingMethod?: null | ShippingMethod;
     shippingAddress?: null | AccountAddress;
     billingAddress?: null | AccountAddress;
     sum: number;
     currency: string;
     payments: Payment[];
     discountCodes: Discount[];
     taxed?: null | Tax;
     /**
      * Access original object from backend
      *
      * This should only be used if you need very specific features
      * right NOW. Please notify Frontastic about your need so that
      * we can integrate those twith the common API. Any usage off
      * this property might make your code unstable against future
      * changes.
      */
     dangerousInnerCart?: any;
}

export interface Discount extends CoreApiDataObject {
     discountId: string;
     code: string;
     state: string;
     name: {[key: string]: string};
     description?: {[key: string]: string};
     /**
      * Amount discounted.
      *
      * On Cart, the amount discounted in the cart.
      * On LineItem, the amount discounted per single line item.
      */
     discountedAmount?: null | number;
     /**
      * Access original object from backend
      *
      * This should only be used if you need very specific features
      * right NOW. Please notify Frontastic about your need so that
      * we can integrate those twith the common API. Any usage off
      * this property might make your code unstable against future
      * changes.
      */
     dangerousInnerDiscount?: any;
}

export interface LineItem extends CoreApiDataObject {
     lineItemId: string;
     name?: string;
     type: string;
     count: number;
     /**
      * Price of a single item
      */
     price: number;
     /**
      * Discounted price per item
      */
     discountedPrice?: null | number;
     /**
      * Translatable discount texts, if any are applied
      */
     discountTexts?: any;
     discounts?: Discount[];
     /**
      * Total price, basically $price * $count, also discounted
      */
     totalPrice: number;
     currency: string;
     isGift: boolean;
     /**
      * Access original object from backend
      *
      * This should only be used if you need very specific features
      * right NOW. Please notify Frontastic about your need so that
      * we can integrate those twith the common API. Any usage off
      * this property might make your code unstable against future
      * changes.
      */
     dangerousInnerItem?: any;
}

export interface Order extends Cart {
     orderId?: string;
     orderVersion?: string;
     orderState?: string;
     createdAt?: any /* \DateTimeImmutable */;
     /**
      * Access original object from backend
      *
      * This should only be used if you need very specific features
      * right NOW. Please notify Frontastic about your need so that
      * we can integrate those twith the common API. Any usage off
      * this property might make your code unstable against future
      * changes.
      */
     dangerousInnerOrder?: any;
}

export interface Payment extends CoreApiDataObject {
     /**
      * An internal ID to identify this payment
      */
     id: string;
     /**
      * The name of the payment service provider
      */
     paymentProvider: string;
     /**
      * The ID used by the payment service provider for this payment
      */
     paymentId: string;
     /**
      * In cent
      */
     amount: number;
     currency: string;
     /**
      * A text describing the current status of the payment
      */
     debug?: string;
     /**
      * One of the `PAYMENT_STATUS_*` constants
      */
     paymentStatus: string;
     version?: number;
     /**
      * The descriptor of the payment method used for this payment
      */
     paymentMethod: string;
     /**
      * This data is stored as is by the `CartApi`. The payment integration can use this to store additional data which
      * might be needed later in the payment process.
      */
     paymentDetails?: any | null;
}

export interface ShippingInfo extends ShippingMethod {
     price: number;
     dangerousInnerShippingInfo?: null | any;
}

export interface ShippingLocation extends CoreApiDataObject {
     /**
      * 2 letter ISO code (https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
      */
     country?: string;
     state?: null | string;
     dangerousInnerShippingLocation?: null | any;
}

export interface ShippingMethod extends CoreApiDataObject {
     shippingMethodId?: string;
     name?: string;
     price?: number;
     /**
      * Localized description of the shipping method.
      */
     description?: string;
     rates?: null | ShippingRate[];
     dangerousInnerShippingMethod?: null | any;
}

export interface ShippingRate extends CoreApiDataObject {
     /**
      * Identifier of the shipping zone.
      */
     zoneId?: string;
     name?: string;
     /**
      * Shipping locations this rate applies to.
      */
     locations?: null | ShippingLocation[];
     /**
      * 3-letter currency code.
      */
     currency?: string;
     /**
      * Price in minor currency (e.g. Cent).
      */
     price?: number;
     dangerousInnerShippingRate?: null | any;
}

export interface Tax extends CoreApiDataObject {
     /**
      * Net amount in cent
      */
     amount: number;
     currency: string;
     taxPortions?: TaxPortion[];
}

export interface TaxPortion extends CoreApiDataObject {
     /**
      * Amount in cent
      */
     amount?: number;
     currency?: string;
     name?: string;
     /**
      * Rate number in the range [0..1]
      */
     rate?: number;
}
