part.query = window.matchMedia?.('(prefers-color-scheme: light)')
part.query.onchange = ({ matches }) => part.parent.setColorMode(!!matches)
part.query.onchange(part.query)