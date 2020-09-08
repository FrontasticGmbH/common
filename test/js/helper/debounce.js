import debounce from '../../../src/js/helper/debounce'

jest.useFakeTimers()

describe('debounce', function () {
    test('debouncing a function', () => {
        const mockFn = jest.fn()
        const debouncedFn = debounce(mockFn, 1000)

        for (let i = 0; i < 50; i++) {
            debouncedFn()
        }
        expect(mockFn).not.toBeCalled()

        jest.runAllTimers()

        expect(mockFn).toBeCalledTimes(1)
    })

    test('immediate param of debounce', () => {
        const mockFn = jest.fn()
        const immediateFn = debounce(mockFn, 1000, true)

        for (let i = 0; i < 10; i++) {
            immediateFn()
        }

        expect(mockFn).toBeCalled()

        jest.runAllTimers()

        expect(mockFn).toBeCalledTimes(1)
    })
})
