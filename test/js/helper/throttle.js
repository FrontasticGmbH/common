import throttle from '../../../src/js/helper/throttle'

jest.useFakeTimers()

describe('throttle', function () {
    test('throttle a function', () => {
        const mockFn = jest.fn()
        const throttledFn = throttle(mockFn, 100)

        expect(mockFn).not.toBeCalled()

        throttledFn()
        throttledFn()
        throttledFn()

        expect(setTimeout).toHaveBeenCalledTimes(1)

        jest.advanceTimersByTime(110)

        throttledFn()
        throttledFn()
        throttledFn()

        expect(setTimeout).toHaveBeenCalledTimes(2)
    })
})
