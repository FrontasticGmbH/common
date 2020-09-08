
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
     attributes?: Content.ContentApi.Attribute[];
     dangerousInnerContent?: any;
}
