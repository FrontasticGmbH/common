/* global spyOn */

import Schema from '../../../src/js/configuration/schema.js'

import fs from 'fs'
import path from 'path'

/*
 * This is a regression test that ensures schema handling between JavaScript and PHP implementation does not differ.
 */

const loadRegressionExamples = () => {
    const fixtureBase = path.join(__dirname, '..', '..', '_fixture', 'configuration')
    return fs.readdirSync(
        fixtureBase
    ).map((directory) => {
        return {
            exampleName: directory,
            inputFixture: (JSON.parse(fs.readFileSync(
                path.join(fixtureBase, directory, 'input_fixture.json')).toString()
            )),
            outputExpectation: (JSON.parse(fs.readFileSync(
                path.join(fixtureBase, directory, 'output_expectation.json')).toString()
            )),
        }
    })
}

describe.each(loadRegressionExamples())("A schema", ({ exampleName, inputFixture, outputExpectation }) => {
    beforeEach(function () {
        spyOn(console, 'warn')
    })

    it(exampleName, () => {
        const schema = new Schema(inputFixture.schema, inputFixture.configuration || {})

        outputExpectation.forEach((expectation) => {
            expect(schema.get(expectation.key)).toStrictEqual(expectation.value)

            if (expectation.warning) {
                expect(console.warn).toHaveBeenCalled()
            } else {
                expect(console.warn).not.toHaveBeenCalled()
            }
        })
    })
})
