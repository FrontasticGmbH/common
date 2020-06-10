
export namespace AccountNS {

    export interface Account {
         accountId?: string;
         email?: string;
         salutation?: string;
         firstName?: string;
         lastName?: string;
         birthday?: any /* \DateTime */;
         data?: any;
         groups?: AccountNS.Group[];
         confirmationToken?: string;
         confirmed?: string;
         tokenValidUntil?: any /* \DateTime */;
         addresses?: AccountNS.Address[];
         authToken?: string | null;
         dangerousInnerAccount?: any;
    }

    export interface Address {
         addressId?: string;
         salutation?: string;
         firstName?: string;
         lastName?: string;
         streetName?: string;
         streetNumber?: string;
         additionalStreetInfo?: string;
         additionalAddressInfo?: string;
         postalCode?: string;
         city?: string;
         country?: string;
         state?: string;
         phone?: string;
         isDefaultBillingAddress?: boolean;
         isDefaultShippingAddress?: boolean;
         dangerousInnerAddress?: any;
    }

    export interface AuthentificationInformation {
         email?: string;
         password?: string;
         newPassword?: string;
    }

    export interface Group {
         groupId?: string;
         name?: string;
         permissions?: string[];
    }

    export interface MetaData {
         author?: string;
         changed?: any /* \DateTimeImmutable */;
    }

    export interface PasswordResetToken {
         email?: string;
         confirmationToken?: string | null;
         tokenValidUntil?: any /* \DateTime */ | null;
    }

    export interface Session {
         loggedIn?: boolean;
         account?: AccountNS.Account;
         message?: string;
    }
}

export namespace CartNS {

    export interface Cart {
         cartId?: string;
         cartVersion?: string;
         custom?: any;
         lineItems?: CartNS.LineItem[];
         email?: string;
         birthday?: any /* \DateTimeImmutable */;
         shippingMethod?: null | CartNS.ShippingMethod;
         shippingAddress?: null | AccountNS.Address;
         billingAddress?: null | AccountNS.Address;
         sum?: number;
         currency?: string;
         payments?: CartNS.Payment[];
         discountCodes?: string[];
         dangerousInnerCart?: any;
    }

    export interface Discount {
         discountId?: string;
         code?: string;
         state?: string;
         name?: NS.Translatable;
         description?: NS.Translatable;
         dangerousInnerDiscount?: any;
    }

    export interface LineItem {
         lineItemId?: string;
         name?: string;
         type?: string;
         custom?: any;
         count?: number;
         price?: number;
         discountedPrice?: number;
         discountTexts?: any;
         totalPrice?: number;
         currency?: string;
         isGift?: boolean;
         dangerousInnerItem?: any;
    }

    export namespace LineItemNS {

        export interface Variant extends CartNS.LineItem {
             variant?: ProductNS.Variant;
             type?: string;
        }
    }

    export interface Order extends CartNS.Cart {
         orderId?: string;
         orderVersion?: string;
         orderState?: string;
         createdAt?: any /* \DateTimeImmutable */;
         dangerousInnerOrder?: any;
    }

    export interface Payment {
         id?: string;
         paymentProvider?: string;
         paymentId?: string;
         amount?: number;
         currency?: string;
         debug?: string;
         paymentStatus?: any;
         version?: any;
         paymentMethod?: any;
    }

    export interface ShippingMethod {
         name?: string;
         price?: number;
    }
}

export namespace ContentNS {

    export interface AttributeFilter {
         name?: string;
         value?: string;
    }

    export namespace ContentApiNS {

        export interface Attribute {
             attributeId?: any;
             content?: string;
             type?: string;
        }

        export interface Content {
             contentId?: string;
             contentTypeId?: string;
             name?: string;
             slug?: string;
             attributes?: ContentNS.ContentApiNS.Attribute[];
             dangerousInnerContent?: any;
        }
    }

    export interface ContentType {
         contentTypeId?: string;
         name?: string;
    }

    export interface Query {
         contentType?: string;
         query?: string;
         contentIds?: any;
         attributes?: ContentNS.AttributeFilter[];
    }

    export interface Result {
         offset?: number;
         total?: number;
         count?: number;
         items?: any;
    }
}

export namespace CoreNS {

    export interface ErrorResult {
         ok?: boolean;
         message?: string;
         endpoint?: string;
         file?: string;
         line?: number;
         stack?: string[];
         code?: string;
         parameters?: any;
    }
}

export namespace NS {

    export namespace HttpClientNS {

        export interface Configuration {
             options?: NS.HttpClientNS.Options;
             defaultHeaders?: string[];
             signatureSecret?: null | string;
             collectStats?: boolean;
             collectProfiling?: boolean;
        }

        export interface Options {
             timeout?: number | number;
        }

        export interface Response {
             status?: number;
             headers?: string[];
             body?: string;
        }
    }
}

export namespace ProductNS {

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
         variants: ProductNS.Variant[];
         dangerousInnerProduct?: any;
    }

    export namespace ProductApiNS {

        export interface FacetDefinition {
             attributeType?: string;
             attributeId?: string;
        }

        export interface Locale {
             language?: string;
             territory?: string;
             country?: string;
             currency?: string;
             original?: string;
        }

        export interface PaginatedQuery extends ProductNS.ProductApiNS.Query {
             limit?: number;
             offset?: number;
        }

        export interface Query {
             locale?: string;
             loadDangerousInnerData?: boolean;
        }

        export interface Result {
             offset?: number;
             total?: number;
             count?: number;
             items?: any;
             facets?: any /* \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet */[];
             query?: ProductNS.ProductApiNS.Query;
        }
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
}

export namespace ProjectNS {

    export interface Attribute {
         attributeId?: string;
         type?: string;
         label?: Map<string, string> | null;
         values?: null | any;
    }
}

export namespace ReplicatorNS {

    export interface Command {
         command?: string;
         channel?: string;
         customer?: string;
         payload?: any;
    }

    export interface Customer {
         name?: string;
         secret?: string;
         edition?: string;
         hasPaasModifications?: boolean;
         features?: any;
         isTransient?: boolean;
         configuration?: any;
         environments?: any;
         projects?: ReplicatorNS.Project[];
    }

    export interface Endpoint {
         name?: string;
         url?: string;
         push?: boolean;
         environment?: string;
    }

    export interface Project {
         projectId?: string;
         name?: string;
         customer?: string;
         apiKey?: string;
         previewUrl?: string;
         publicUrl?: string;
         webpackPort?: number;
         ssrPort?: number;
         configuration?: any;
         data?: any;
         languages?: string[];
         defaultLanguage?: string;
         projectSpecific?: string[];
         endpoints?: ReplicatorNS.Endpoint[];
    }

    export interface Result {
         ok?: boolean;
         payload?: any;
         message?: string;
         file?: string;
         line?: number;
         stack?: any;
    }
}

export namespace SapCommerceCloudNS {

    export namespace LocaleNS {

        export interface SapLocale {
             languageCode?: string;
             currencyCode?: string;
        }
    }
}

export namespace ShopwareNS {

    export namespace LocaleNS {

        export interface ShopwareLocale {
             country?: string;
             countryId?: string;
             currency?: string;
             currencyId?: string;
             language?: string;
             languageId?: string;
        }
    }

    export namespace ProjectConfigApiNS {

        export interface ShopwareCountry {
             id?: string;
             name?: string;
             iso?: string;
             iso3?: string;
             taxFree?: boolean;
             active?: boolean;
             shippingAvailable?: boolean;
             position?: number;
        }

        export interface ShopwareCurrency {
             id?: string;
             factor?: number;
             name?: string;
             shortName?: string;
             symbol?: string;
             isoCode?: string;
             isSystemDefault?: boolean;
             decimalPrecision?: number;
        }

        export interface ShopwareLanguage {
             id?: string;
             name?: string;
             localeId?: string;
             localeCode?: string;
             localeName?: string;
             localeTerritory?: string;
        }

        export interface ShopwarePaymentMethod {
             id?: string;
             name?: string;
             description?: string;
             active?: boolean;
             position?: number;
        }

        export interface ShopwareSalutation {
             id?: string;
             salutationKey?: string;
             displayName?: string;
             letterName?: string;
        }

        export interface ShopwareShippingMethod {
             id?: string;
             name?: string;
             active?: boolean;
             deliveryTime?: ShopwareNS.ProjectConfigApiNS.ShopwareShippingMethodDeliveryTime;
        }

        export interface ShopwareShippingMethodDeliveryTime {
             id?: string;
             name?: string;
        }
    }
}

export namespace WishlistNS {

    export interface LineItem {
         lineItemId?: string;
         name?: string;
         type?: string;
         addedAt?: any /* \DateTimeImmutable */;
         count?: number;
         dangerousInnerItem?: any;
    }

    export namespace LineItemNS {

        export interface Variant extends WishlistNS.LineItem {
             variant?: ProductNS.Variant;
             type?: string;
        }
    }

    export interface Wishlist {
         wishlistId?: string;
         wishlistVersion?: string;
         anonymousId?: string;
         accountId?: string;
         name?: string[];
         lineItems?: WishlistNS.LineItem[];
         dangerousInnerWishlist?: any;
    }
}
