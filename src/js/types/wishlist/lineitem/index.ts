
import {
    LineItem,
} from '..'

import {
    Variant,
} from '../../product'

export interface Variant extends LineItem {
     variant?: Variant;
     type?: string;
}
