
import {
    LineItem as WishlistLineItem,
} from '..'

import {
    Variant as ProductVariant,
} from '../../product'

export interface Variant extends WishlistLineItem {
     variant?: ProductVariant;
     type?: string;
}
