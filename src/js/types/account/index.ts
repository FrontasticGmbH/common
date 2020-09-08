
import {
    ApiDataObject as CoreApiDataObject,
} from '../core'

export interface Account extends CoreApiDataObject {
     accountId?: string;
     email?: string;
     salutation?: string;
     firstName?: string;
     lastName?: string;
     birthday?: any /* \DateTime */;
     groups?: Group[];
     confirmationToken?: string;
     confirmed?: string;
     tokenValidUntil?: any /* \DateTime */;
     addresses?: Address[];
     authToken?: string | null;
     dangerousInnerAccount?: any;
}

export interface Address extends CoreApiDataObject {
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
     account?: Account;
     message?: string;
}
