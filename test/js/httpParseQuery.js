
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
})
