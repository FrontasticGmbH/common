
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
     deliveryTime?: ShopwareShippingMethodDeliveryTime;
}

export interface ShopwareShippingMethodDeliveryTime {
     id?: string;
     name?: string;
}
