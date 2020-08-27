import omit from '../../../src/js/helper/omit'

test.each([
    [
        { a: 1, b: 2, c: 3},
        ['b'], 
        { a: 1, c: 3 }
    ],
    [
        { a: 1, b: 2, c: 3},
        ['b', 'unknown'], 
        { a: 1, c: 3 }
    ],
    [
        { a: 1, b: 2, c: 3},
        ['unknown'], 
        { a: 1, b: 2, c: 3 }
    ],
    [
        [1, 2, 3],
        ['1'], 
        { '0': 1, '2': 3 }
    ],
])('omit throws out object keys', (input, keys, expectation) => {
    expect(omit(input, keys)).toEqual(expectation)
})
