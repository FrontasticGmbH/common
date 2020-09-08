
export interface Category {
     categoryId?: string;
     name?: string;
     depth?: number;
     path?: string;
     slug?: string;
     dangerousInnerCategory?: any;
}

export interface Product {
     productId: string;
     changed?: null | any /* \DateTimeImmutable */;
     version?: null | string;
     name: string;
     slug: string;
     description?: string;
     categories?: string[];
     variants: Product.Variant[];
     dangerousInnerProduct?: any;
}

export interface ProductType {
     productTypeId?: string;
     name?: string;
     dangerousInnerProductType?: any;
}

export interface Variant {
     id: string;
     sku: string;
     groupId?: string;
     price: number;
     discountedPrice?: null | number;
     discounts?: any;
     currency?: string;
     attributes?: any;
     images?: any;
     isOnStock?: boolean;
     dangerousInnerVariant?: any;
}
