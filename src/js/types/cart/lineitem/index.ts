
import {
    LineItem as CartLineItem,
} from '..'

import {
    Variant as ProductVariant,
} from '../../product'

export interface Variant extends CartLineItem {
     variant?: ProductVariant;
     type?: string;
}
