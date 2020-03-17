import Paginate from '../../test/js/paginate'
import _ from 'lodash'

const buildStream = (firstPage, lastPage, currentPage, addition = 5) => {
    return {
        total: lastPage * 10 + addition,
        query: {
            offset: currentPage * 10,
            limit: 10,
        },
    }
}

it('Creates pages without gaps', () => {
    expect(_.map(new Paginate(buildStream(0, 5, 2)).getPages(), 'pageIndex')).toEqual([0, 1, 2, 3, 4, 5])
})

it('Creates pages with gap in front', () => {
    expect(_.map(new Paginate(buildStream(0, 7, 6)).getPages(), 'pageIndex')).toEqual([0, 1, undefined, 5, 6, 7])
})

it('Creates pages with gap in back', () => {
    expect(_.map(new Paginate(buildStream(0, 7, 1)).getPages(), 'pageIndex')).toEqual([0, 1, 2, undefined, 6, 7])
})

it('Creates pages with gap on both sides', () => {
    expect(_.map(new Paginate(buildStream(0, 15, 7)).getPages(), 'pageIndex')).toEqual([
        0,
        1,
        undefined,
        6,
        7,
        8,
        undefined,
        14,
        15,
    ])
})

it('Does not show previous button on first page', () => {
    expect(_.map([new Paginate(buildStream(0, 15, 0)).getPrevious()], 'pageIndex')).toEqual([undefined])
})

it('Creates previous button', () => {
    expect(_.map([new Paginate(buildStream(0, 15, 2)).getPrevious()], 'pageIndex')).toEqual([1])
})

it('Does not show next button on last page', () => {
    expect(_.map([new Paginate(buildStream(0, 15, 15)).getNext()], 'pageIndex')).toEqual([undefined])
})

it('Creates next button', () => {
    expect(_.map([new Paginate(buildStream(0, 15, 2)).getNext()], 'pageIndex')).toEqual([3])
})

it('No empty page when divisible by limit', () => {
    expect(new Paginate(buildStream(0, 1, 0, 0)).getPages()).toEqual([
        { offset: 0, pageIndex: 0, pageNumber: 1, selected: true },
    ])
})
