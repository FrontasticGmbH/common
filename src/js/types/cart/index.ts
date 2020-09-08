
import {
    ApiDataObject,
} from '../core'

export interface Cart extends ApiDataObject {
     cartId?: string;
     cartVersion?: string;
     lineItems?: Cart.LineItem[];
     email?: string;
     birthday?: any /* \DateTimeImmutable */;
     shippingMethod?: null | Cart.ShippingMethod;
     shippingAddress?: null | any /* \Frontastic\Common\CartApiBundle\Domain\Address */;
     billingAddress?: null | any /* \Frontastic\Common\CartApiBundle\Domain\Address */;
     sum?: number;
     currency?: string;
     payments?: Cart.Payment[];
     discountCodes?: string[];
     dangerousInnerCart?: any;
}

export interface Discount {
     discountId?: string;
     code?: string;
     state?: string;
     name?: Translatable;
     description?: Translatable;
     dangerousInnerDiscount?: any;
}

export interface LineItem extends ApiDataObject {
     lineItemId?: string;
     name?: string;
     type?: string;
     count?: number;
     price?: number;
     discountedPrice?: number;
     discountTexts?: any;
     totalPrice?: number;
     currency?: string;
     isGift?: boolean;
     dangerousInnerItem?: any;
}

export interface Order extends Cart {
     orderId?: string;
     orderVersion?: string;
     orderState?: string;
     createdAt?: any /* \DateTimeImmutable */;
     dangerousInnerOrder?: any;
}

export interface Payment extends ApiDataObject {
     id?: string;
     paymentProvider?: string;
     paymentId?: string;
     amount?: number;
     currency?: string;
     debug?: string;
     paymentStatus?: string;
     version?: number;
     paymentMethod?: string;
     paymentDetails?: any | null;
}

export interface ShippingMethod {
     name?: string;
     price?: number;
}
