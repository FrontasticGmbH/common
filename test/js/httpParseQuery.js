
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
            second: 'bar',
        })
    })

    it('parses example 2', () => {
        const parserResult = httpParseQuery('str_a=Jack+and+Jill+didn%27t+see+the+well.')

        expect(parserResult).toEqual({
            str_a: 'Jack and Jill didn\'t see the well.',
        })
    })

    it('parses example 3', () => {
        const parserResult = httpParseQuery('abc[a][b]["c"]=def&abc[q]=t+5')

        expect(parserResult).toEqual({
            'abc': {
                'a': {
                    'b': { 'c': 'def' },
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

    it('it parses arrays without indexes', () => {
        const parserResult = httpParseQuery('a[]=foo&a[]=bar')

        expect(parserResult).toEqual({
            'a': ['foo', 'bar'],
        })
    })

    it('it does not pollute the Object prototype', () => {
        const parserResult = httpParseQuery('__proto__["test"]=foo')

        expect(parserResult).toEqual({})

        expect(({}).test).toBeUndefined()
    })

    it('it does not pollute via constructor.prototype', () => {
        const parserResult = httpParseQuery('constructor[prototype][polluted]=yes')

        expect(parserResult).toEqual({})
        expect(({}).polluted).toBeUndefined()
    })

    it('it does not pollute via nested __proto__', () => {
        const parserResult = httpParseQuery('a[__proto__][polluted]=yes')

        expect(parserResult).toEqual({})
        expect(({}).polluted).toBeUndefined()
    })

    it('it does not pollute via single-segment __proto__', () => {
        const parserResult = httpParseQuery('__proto__=evil')

        expect(parserResult).toEqual({})
        expect(Object.getPrototypeOf({})).toBe(Object.prototype)
        expect(({}).constructor).toBe(Object)
    })

    it('it does not pollute via __proto__ as the final segment', () => {
        const parserResult = httpParseQuery('a[__proto__]=evil')

        expect(parserResult).toEqual({})
        expect(({}).a).toBeUndefined()
        expect(Object.getPrototypeOf({})).toBe(Object.prototype)
    })

    it('it does not pollute via quoted __proto__', () => {
        const parserResult = httpParseQuery('%27__proto__%27[polluted]=yes')

        expect(parserResult).toEqual({})
        expect(({}).polluted).toBeUndefined()
    })

    it('it does not pollute via double-quoted __proto__', () => {
        const parserResult = httpParseQuery('%22__proto__%22[polluted]=yes')

        expect(parserResult).toEqual({})
        expect(({}).polluted).toBeUndefined()
    })

    it('it does not pollute via single-segment constructor', () => {
        const parserResult = httpParseQuery('constructor=evil')

        expect(parserResult).toEqual({})
        expect(({}).constructor).toBe(Object)
    })

    it('it does not pollute via single-segment prototype', () => {
        const parserResult = httpParseQuery('prototype=evil')

        expect(parserResult).toEqual({})
        expect(Object.getPrototypeOf({})).toBe(Object.prototype)
    })

    it('it drops only entries with dangerous keys, not benign ones in the same query', () => {
        const parserResult = httpParseQuery('foo=bar&__proto__[polluted]=yes&baz=qux')

        expect(parserResult).toEqual({
            foo: 'bar',
            baz: 'qux',
        })
        expect(({}).polluted).toBeUndefined()
    })
})
