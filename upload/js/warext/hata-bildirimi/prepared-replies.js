(() => {
    'use strict'

    document.addEventListener('change', event => {
        const select = event.target.closest('.js-wrxtPreparedReplySelect')
        if (!select) return

        const composer = select.closest('.js-wrxtPreparedReplyComposer')
        if (!composer) return

        const replyId = parseInt(select.value, 10) || 0
        if (!replyId) return

        const data = composer.querySelector(`[data-wrxt-prepared-reply-id="${replyId}"]`)
        const textarea = composer.querySelector('textarea[name="message"]')
        if (!data || !textarea) return

        const htmlNode = data.querySelector('.js-wrxtPreparedReplyHtml')
        const bbCodeNode = data.querySelector('.js-wrxtPreparedReplyBbCode')
        if (!htmlNode || !bbCodeNode) return

        const html = htmlNode.innerHTML
        const bbCode = bbCodeNode.value
        const editor = window.XF?.Element?.getHandler(textarea, 'editor')

        if (editor?.ed && typeof editor.replaceContent === 'function') {
            editor.replaceContent(html, bbCode)
            if (editor.ed.undo?.saveStep) {
                editor.ed.undo.saveStep()
            }
        } else {
            textarea.value = bbCode
            textarea.dispatchEvent(new Event('input', { bubbles: true }))
            textarea.dispatchEvent(new Event('change', { bubbles: true }))
        }
    })
})()
