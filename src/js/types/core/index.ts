
export interface ApiDataObject {
     rawApiInput?: any | any;
     projectSpecificData?: any;
}

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
