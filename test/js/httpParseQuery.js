
import httpParseQuery from '../../src/js/httpParseQuery'

describe('httpQueryParser', function () {
    it('parses a simple query', () => {
        const parserResult = httpParseQuery('foo=1&bar=baz')

        expect(parserResult).toEqual({
            foo: '1',
            bar: 'baz',
        })
    })

    it('parses example 1', () => {
        const parserResult = httpParseQuery('first=foo&second=bar')

        expect(parserResult).toEqual({
            first: 'foo',
            second: 'bar'
        })
    })

    it('parses example 2', () => {
        const parserResult = httpParseQuery('str_a=Jack+and+Jill+didn%27t+see+the+well.')

        expect(parserResult).toEqual({
            str_a: 'Jack and Jill didn\'t see the well.'
        })
    })

    it('parses example 3', () => {
        const parserResult = httpParseQuery('abc[a][b]["c"]=def&abc[q]=t+5')

        expect(parserResult).toEqual({
            'abc': {
                'a': {
                    'b': { 'c':'def' },
                },
                'q': 't 5',
            },
        })
    })

    it('it parses pure array to array', () => {
        const parserResult = httpParseQuery('a[0]=foo&a[1]=bar')

        expect(parserResult).toEqual({
            'a': ['foo', 'bar'],
        })
    })

    it('it parses non-consecutive array to object', () => {
        const parserResult = httpParseQuery('a[0]=foo&a[2]=bar')

        expect(parserResult).toEqual({
            'a': {
                '0': 'foo',
                '2': 'bar',
            },
        })
    })

    it('it parses array in deep nested structure', () => {
        const parserResult = httpParseQuery('a[x][y][0]=foo&a[x][y][1]=bar&a[x][z]=bam')

        expect(parserResult).toEqual({
            'a': {
                'x': {
                    'y': ['foo', 'bar'],
                    'z': 'bam',
                },
            },
        })
    })
})
