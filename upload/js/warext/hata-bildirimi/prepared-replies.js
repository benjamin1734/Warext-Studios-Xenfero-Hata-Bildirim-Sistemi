(() => {
    'use strict'

    const findEditorTextarea = composer => composer.querySelector(
        'textarea[data-original-name="message"][data-xf-init~="editor"], textarea[data-original-name="message"], textarea[name="message"].js-editor, textarea[name="message"]'
    )

    const replaceEditorContent = (composer, html, bbCode) => {
        const textarea = findEditorTextarea(composer)
        if (!textarea) {
            console.error('Warext hazır cevap: XenForo cevap editörü bulunamadı.')
            return false
        }

        const bbCodeInput = composer.querySelector('input[type="hidden"][data-bb-code="message"]')
        const editor = window.XF?.Element?.getHandler?.(textarea, 'editor')

        if (editor && typeof editor.replaceContent === 'function') {
            editor.replaceContent(html, bbCode)
            if (editor.ed?.undo?.saveStep) {
                editor.ed.undo.saveStep()
            }
            if (bbCodeInput) {
                bbCodeInput.value = bbCode
            }
            return true
        }

        const froala = window.FroalaEditor?.INSTANCES?.find(instance => {
            const original = instance?.$oel?.[0] || instance?.el || null
            return original === textarea
        })

        if (froala) {
            if (froala.bbCode?.isBbCodeView?.() && froala.bbCode?.replaceBbCode) {
                froala.bbCode.replaceBbCode(bbCode)
            } else if (froala.html?.set) {
                froala.html.set(html)
            }

            froala.undo?.saveStep?.()
            if (bbCodeInput) {
                bbCodeInput.value = bbCode
            }
            textarea.dispatchEvent(new Event('input', { bubbles: true }))
            textarea.dispatchEvent(new Event('change', { bubbles: true }))
            return true
        }

        if (bbCodeInput) {
            bbCodeInput.value = bbCode
        }
        textarea.value = textarea.dataset.originalName ? html : bbCode
        textarea.dispatchEvent(new Event('input', { bubbles: true }))
        textarea.dispatchEvent(new Event('change', { bubbles: true }))
        return true
    }

    document.addEventListener('change', event => {
        const select = event.target.closest('.js-wrxtPreparedReplySelect')
        if (!select) return

        const composer = select.closest('.js-wrxtPreparedReplyComposer')
        if (!composer) return

        const replyId = parseInt(select.value, 10) || 0
        if (!replyId) return

        const data = composer.querySelector(`[data-wrxt-prepared-reply-id="${replyId}"]`)
        if (!data) return

        const htmlNode = data.querySelector('.js-wrxtPreparedReplyHtml')
        const bbCodeNode = data.querySelector('.js-wrxtPreparedReplyBbCode')
        if (!htmlNode || !bbCodeNode) return

        replaceEditorContent(composer, htmlNode.innerHTML, bbCodeNode.value)
    })
})()
