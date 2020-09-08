export default (object, keys) => {
    return Object.fromEntries(
        Object.entries(object).filter(([key]) => { return !keys.includes(key) })
    )
}
