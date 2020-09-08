
export interface LineItem {
     lineItemId?: string;
     name?: string;
     type?: string;
     addedAt?: any /* \DateTimeImmutable */;
     count?: number;
     dangerousInnerItem?: any;
}

export interface Wishlist {
     wishlistId?: string;
     wishlistVersion?: string;
     anonymousId?: string;
     accountId?: string;
     name?: string[];
     lineItems?: Wishlist.LineItem[];
     dangerousInnerWishlist?: any;
}
