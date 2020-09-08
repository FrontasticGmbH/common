
export interface Configuration {
     options?: HttpClient.Options;
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
     rawApiOutput?: any /* \Psr\Http\Message\ResponseInterface */;
}
