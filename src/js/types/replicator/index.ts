
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
     projects?: Replicator.Project[];
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
     endpoints?: Replicator.Endpoint[];
}

export interface Result {
     ok?: boolean;
     payload?: any;
     message?: string;
     file?: string;
     line?: number;
     stack?: any;
}
