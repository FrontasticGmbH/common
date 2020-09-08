
import {
    LineItem,
} from '..'

export interface Variant extends LineItem {
     variant?: Product.Variant;
     type?: string;
}
