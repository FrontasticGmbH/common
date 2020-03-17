import _ from 'lodash'

export default class Pagination {
    constructor (productStream) {
        this.stream = productStream
        this.currentPage = Math.floor(productStream.query.offset / productStream.query.limit)
        this.firstPage = 0
        this.lastPage = Math.floor((productStream.total - 1) / productStream.query.limit)

        this.delta = 1
    }

    paginate = (firstPage, lastPage, currentPage, delta = 2) => {
        let pageNumbers = []
        for (let i = firstPage; i <= lastPage; i++) {
            if (
                i <= firstPage + delta ||
                i >= lastPage - delta ||
                (i >= currentPage - delta && i <= currentPage + delta)
            ) {
                pageNumbers.push(i)
            }
        }

        let pageNumbersWithGaps = []
        for (let index in pageNumbers) {
            if (pageNumbers[index - 1] && pageNumbers[index - 1] < pageNumbers[index] - 1) {
                pageNumbersWithGaps.push(null)
            }
            pageNumbersWithGaps.push(pageNumbers[index])
        }
        return pageNumbersWithGaps
    }

    getPrevious () {
        if (this.currentPage < 1) {
            return null
        }

        return {
            pageIndex: this.currentPage - 1,
            pageNumber: this.currentPage,
            offset: (this.currentPage - 1) * this.stream.query.limit,
            selected: false,
        }
    }

    getPages () {
        return _.map(this.paginate(this.firstPage, this.lastPage, this.currentPage, this.delta), (page) => {
            if (page === null) {
                return null
            }

            return {
                pageIndex: page,
                pageNumber: page + 1,
                offset: page * this.stream.query.limit,
                selected: this.currentPage === page,
            }
        })
    }

    getNext () {
        if (this.currentPage >= this.lastPage) {
            return null
        }

        return {
            pageIndex: this.currentPage + 1,
            pageNumber: this.currentPage + 2,
            offset: (this.currentPage + 1) * this.stream.query.limit,
            selected: false,
        }
    }
}
