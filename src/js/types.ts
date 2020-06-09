
import {
    ReplicatorBundleNS,
    AccountApiBundleNS,
    ProductApiBundleNS,
} from '@frontastic/common/src/js/types'

export namespace ApiCoreBundleNS {

    export namespace DomainNS {

        export interface App {
             appId?: string;
             identifier?: string;
             sequence?: string;
             name?: string;
             description?: string;
             configurationSchema?: any /* \StdClass */;
             environment?: string;
             metaData?: any /* \Frontastic\UserBundle\Domain\MetaData */;
        }

        export interface AppRepository {
             app?: string;
             sequence?: string;
        }

        export namespace AppNS {

            export interface FeatureFlag {
                 locale?: string;
                 dataId?: string;
                 sequence?: string;
                 isDeleted?: boolean;
                 key?: string;
                 on?: boolean;
                 onStaging?: boolean;
                 onDevelopment?: boolean;
                 description?: any /* \Frontastic\Catwalk\ApiCoreBundle\Domain\App\text */;
            }

            export interface Storefinder {
                 locale?: string;
                 dataId?: string;
                 sequence?: string;
                 isDeleted?: boolean;
                 name?: string;
                 image?: any;
                 description?: any /* \Frontastic\Catwalk\ApiCoreBundle\Domain\App\text */;
                 people?: any;
                 street?: string;
                 street_ammendment?: string;
                 zip?: string;
                 city?: string;
                 state?: string;
                 country?: string;
                 latitude?: number;
                 longitude?: number;
            }

            export interface Teaser {
                 locale?: string;
                 dataId?: string;
                 sequence?: string;
                 isDeleted?: boolean;
                 identifier?: string;
                 image1?: any;
                 text2?: any /* \Frontastic\Catwalk\ApiCoreBundle\Domain\App\text */;
                 text3?: any /* \Frontastic\Catwalk\ApiCoreBundle\Domain\App\text */;
                 image4?: any;
            }
        }

        export interface Context {
             environment?: string;
             customer?: ReplicatorBundleNS.DomainNS.Customer;
             project?: ReplicatorBundleNS.DomainNS.Project;
             projectConfiguration?: any;
             projectConfigurationSchema?: any;
             locale?: string;
             currency?: string;
             routes?: string[];
             session?: AccountApiBundleNS.DomainNS.Session;
             featureFlags?: any;
             host?: string;
        }

        export interface Tastic {
             tasticId?: string;
             tasticType?: string;
             sequence?: string;
             name?: string;
             description?: string;
             configurationSchema?: any /* \StdClass */;
             environment?: string;
             metaData?: any /* \Frontastic\UserBundle\Domain\MetaData */;
             isDeleted?: boolean;
        }
    }
}

export namespace FrontendBundleNS {

    export namespace DomainNS {

        export interface Cell {
             cellId?: string;
             configuration?: FrontendBundleNS.DomainNS.CellNS.Configuration;
             customConfiguration?: null | any /* \stdClass */;
             tastics?: FrontendBundleNS.DomainNS.Tastic[];
        }

        export namespace CellNS {

            export interface Configuration extends FrontendBundleNS.DomainNS.Configuration {
                 size?: any;
            }
        }

        export interface Configuration {
             mobile?: boolean;
             tablet?: boolean;
             desktop?: boolean;
        }

        export interface Facet extends ProductApiBundleNS.DomainNS.ProductApiNS.FacetDefinition {
             facetId?: string;
             sequence?: string;
             sort?: number;
             isEnabled?: boolean;
             label?: null | any;
             urlIdentifier?: string;
             facetOptions?: any;
             metaData?: any /* \Frontastic\Catwalk\FrontendBundle\Domain\MetaData */;
             isDeleted?: boolean;
        }

        export interface Layout {
             layoutId?: string;
             sequence?: string;
             name?: string;
             description?: string;
             image?: string;
             regions?: string[];
             metaData?: any /* \Frontastic\UserBundle\Domain\MetaData */;
        }

        export interface MasterPageMatcherRules {
             rulesId?: string;
             rules?: any;
             sequence?: string;
             metaData?: any /* \Frontastic\UserBundle\Domain\MetaData */;
        }

        export interface Node {
             nodeId?: string;
             isMaster?: boolean;
             sequence?: string;
             configuration?: any;
             streams?: FrontendBundleNS.DomainNS.Stream[];
             name?: string;
             path?: string[];
             depth?: number;
             sort?: number;
             children?: FrontendBundleNS.DomainNS.Node[];
             metaData?: any /* \Frontastic\Backstage\UserBundle\Domain\MetaData */;
             error?: null | string;
             isDeleted?: boolean;
        }

        export interface Page {
             pageId?: string;
             sequence?: string;
             node?: FrontendBundleNS.DomainNS.Node;
             layoutId?: string;
             regions?: FrontendBundleNS.DomainNS.Region[];
             metaData?: any /* \Frontastic\UserBundle\Domain\MetaData */;
             isDeleted?: boolean;
             state?: string;
             scheduledFromTimestamp?: null | number;
             scheduledToTimestamp?: null | number;
             nodesPagesOfTypeSortIndex?: null | number;
             scheduleCriterion?: string;
        }

        export namespace PageMatcherNS {

            export interface PageMatcherContext {
                 entity?: null | any;
                 categoryId?: null | string;
                 productId?: null | string;
                 contentId?: null | string;
                 search?: null | string;
                 cart?: null | any;
                 checkout?: null | any;
                 checkoutFinished?: null | any;
                 orderId?: null | string;
                 account?: null | any;
                 accountForgotPassword?: null | any;
                 accountConfirm?: null | any;
                 accountProfile?: null | any;
                 accountAddresses?: null | any;
                 accountOrders?: null | any;
                 accountWishlists?: null | any;
                 accountVouchers?: null | any;
                 error?: null | any;
            }
        }

        export interface Preview {
             previewId?: string;
             createdAt?: any /* \DateTime */;
             node?: FrontendBundleNS.DomainNS.Node;
             page?: FrontendBundleNS.DomainNS.Page;
             metaData?: any /* \FrontendBundle\UserBundle\Domain\MetaData */;
        }

        export interface ProjectConfiguration {
             projectConfigurationId?: string;
             configuration?: any;
             metaData?: any /* \Frontastic\Backstage\UserBundle\Domain\MetaData */;
             sequence?: string;
             isDeleted?: boolean;
        }

        export interface Redirect {
             redirectId?: string;
             sequence?: string;
             path?: string;
             query?: string;
             targetType?: string;
             target?: string;
             language?: null | string;
             metaData?: any /* \Frontastic\Backstage\UserBundle\Domain\MetaData */;
             isDeleted?: boolean;
        }

        export interface Region {
             regionId?: string;
             configuration?: FrontendBundleNS.DomainNS.RegionNS.Configuration;
             elements?: FrontendBundleNS.DomainNS.Cell[];
             cells?: FrontendBundleNS.DomainNS.Cell[];
        }

        export namespace RegionNS {

            export interface Configuration extends FrontendBundleNS.DomainNS.Configuration {
                 flexDirection?: string;
                 flexWrap?: string;
                 justifyContent?: string;
                 alignItems?: string;
                 alignContent?: string;
            }
        }

        export interface Route {
             nodeId?: string;
             route?: string;
             locale?: null | string;
        }

        export interface Schema {
             schemaId?: string;
             schemaType?: string;
             schema?: any;
             metaData?: any /* \Frontastic\Backstage\UserBundle\Domain\MetaData */;
             sequence?: string;
             isDeleted?: boolean;
        }

        export interface Stream {
             streamId?: string;
             type?: string;
             name?: string;
             configuration?: any;
             tastics?: FrontendBundleNS.DomainNS.Tastic[];
        }

        export interface StreamContext {
             node?: FrontendBundleNS.DomainNS.Node;
             page?: FrontendBundleNS.DomainNS.Page;
             context?: ApiCoreBundleNS.DomainNS.Context;
             usingTastics?: FrontendBundleNS.DomainNS.Tastic[];
             parameters?: any;
        }

        export interface Tastic {
             tasticId?: string;
             tasticType?: string;
             configuration?: FrontendBundleNS.DomainNS.TasticNS.Configuration;
        }

        export namespace TasticNS {

            export interface Configuration extends FrontendBundleNS.DomainNS.Configuration {
            }
        }

        export interface ViewData {
             stream?: any;
             tastic?: any;
        }
    }
}
