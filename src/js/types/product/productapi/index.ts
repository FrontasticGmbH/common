
import {
    ApiDataObject,
} from '../../core'

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

export interface PaginatedQuery extends Query {
     limit?: number;
     offset?: number;
     cursor?: string;
}

export interface Query extends ApiDataObject {
     locale?: string;
     loadDangerousInnerData?: boolean;
}

export interface Result {
     offset?: number;
     total?: number;
     previousCursor?: string;
     nextCursor?: string;
     count?: number;
     items?: any;
     facets?: any /* \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet */[];
     query?: Product.ProductApi.Query;
}
