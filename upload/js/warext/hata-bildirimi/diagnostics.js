(() => {
    'use strict'

    if (window.WrxtHataDiagnostics)
    {
        return
    }

    const clientKey = 'wrxtHataClientErrors'
    const networkKey = 'wrxtHataNetworkErrors'
    const maxItems = 8
    const maxAge = 15 * 60 * 1000
    const memory = { client: [], network: [] }

    const now = () => Date.now()

    const cleanUrl = value => {
        if (!value)
        {
            return ''
        }

        try
        {
            const url = new URL(String(value), window.location.href)
            if (url.protocol !== 'http:' && url.protocol !== 'https:')
            {
                return ''
            }
            return (url.origin + url.pathname).slice(0, 2048)
        }
        catch (e)
        {
            return ''
        }
    }

    const cleanText = (value, maxLength) => {
        let text = String(value || '')
        if (!text)
        {
            return ''
        }

        text = text.replace(
            /\b(password|passwd|authorization|bearer|token|csrf|_xfToken|api[_-]?key)\b\s*[:=]\s*[^\s,;]+/giu,
            '$1=[redacted]'
        )
        text = text.replace(/https?:\/\/[^\s)\]}]+/giu, match => cleanUrl(match) || '[url]')
        return text.slice(0, maxLength)
    }

    const read = (key, fallback) => {
        try
        {
            const parsed = JSON.parse(sessionStorage.getItem(key) || '[]')
            return Array.isArray(parsed) ? parsed : fallback
        }
        catch (e)
        {
            return fallback
        }
    }

    const write = (key, items) => {
        try
        {
            sessionStorage.setItem(key, JSON.stringify(items))
        }
        catch (e)
        {
        }
    }

    const fresh = items => {
        const minimum = now() - maxAge
        return items
            .filter(item => item && Number(item.time || 0) >= minimum)
            .slice(-maxItems)
    }

    const add = (kind, item) => {
        const key = kind === 'client' ? clientKey : networkKey
        const fallback = kind === 'client' ? memory.client : memory.network
        const items = fresh(read(key, fallback))
        items.push(item)
        const result = items.slice(-maxItems)

        if (kind === 'client')
        {
            memory.client = result
        }
        else
        {
            memory.network = result
        }

        write(key, result)
    }

    const ignoredRequest = value => {
        const url = cleanUrl(value)
        if (!url)
        {
            return true
        }

        try
        {
            const path = new URL(url).pathname
            return /\/hata-bildir(?:\/|$)/i.test(path)
        }
        catch (e)
        {
            return false
        }
    }

    window.addEventListener('error', event => {
        if (!event || (!event.message && !event.error))
        {
            return
        }

        add('client', {
            type: 'error',
            message: cleanText(event.message || event.error?.message || 'JavaScript error', 1000),
            source: cleanUrl(event.filename || ''),
            line: Number(event.lineno || 0),
            column: Number(event.colno || 0),
            stack: cleanText(event.error?.stack || '', 1500),
            time: now()
        })
    }, true)

    window.addEventListener('unhandledrejection', event => {
        const reason = event?.reason
        const message = typeof reason === 'string'
            ? reason
            : (reason?.message || 'Unhandled promise rejection')

        add('client', {
            type: 'unhandledrejection',
            message: cleanText(message, 1000),
            source: '',
            line: 0,
            column: 0,
            stack: cleanText(reason?.stack || '', 1500),
            time: now()
        })
    })

    if (typeof window.fetch === 'function')
    {
        const originalFetch = window.fetch
        window.fetch = function(input, init)
        {
            const requestUrl = typeof input === 'string' ? input : (input?.url || '')
            const method = String(init?.method || input?.method || 'GET').toUpperCase()

            return originalFetch.apply(this, arguments).then(response => {
                if (response && response.status >= 400 && !ignoredRequest(requestUrl))
                {
                    add('network', {
                        transport: 'fetch',
                        method,
                        status: Number(response.status || 0),
                        url: cleanUrl(requestUrl),
                        message: cleanText(response.statusText || '', 500),
                        time: now()
                    })
                }
                return response
            }).catch(error => {
                if (!ignoredRequest(requestUrl))
                {
                    add('network', {
                        transport: 'fetch',
                        method,
                        status: 0,
                        url: cleanUrl(requestUrl),
                        message: cleanText(error?.message || 'Network error', 500),
                        time: now()
                    })
                }
                throw error
            })
        }
    }

    if (window.XMLHttpRequest)
    {
        const originalOpen = XMLHttpRequest.prototype.open
        const originalSend = XMLHttpRequest.prototype.send

        XMLHttpRequest.prototype.open = function(method, url)
        {
            this.__wrxtHataMethod = String(method || 'GET').toUpperCase()
            this.__wrxtHataUrl = String(url || '')
            return originalOpen.apply(this, arguments)
        }

        XMLHttpRequest.prototype.send = function()
        {
            if (!this.__wrxtHataBound)
            {
                this.__wrxtHataBound = true
                this.addEventListener('loadend', () => {
                    const status = Number(this.status || 0)
                    if ((status === 0 || status >= 400) && !ignoredRequest(this.__wrxtHataUrl))
                    {
                        add('network', {
                            transport: 'xhr',
                            method: this.__wrxtHataMethod || 'GET',
                            status,
                            url: cleanUrl(this.__wrxtHataUrl),
                            message: cleanText(this.statusText || '', 500),
                            time: now()
                        })
                    }
                })
            }

            return originalSend.apply(this, arguments)
        }
    }

    const collect = () => {
        const client = fresh(read(clientKey, memory.client))
        const network = fresh(read(networkKey, memory.network))
        memory.client = client
        memory.network = network

        return { client, network }
    }

    const populate = form => {
        if (!form)
        {
            return
        }

        const data = collect()
        const clientInput = form.querySelector('[name="client_errors_json"]')
        const networkInput = form.querySelector('[name="network_errors_json"]')

        if (clientInput)
        {
            clientInput.value = JSON.stringify(data.client)
        }
        if (networkInput)
        {
            networkInput.value = JSON.stringify(data.network)
        }
    }

    window.WrxtHataDiagnostics = {
        collect,
        populate
    }
})()
