// bip sound
// http://soundbible.com/mp3/A-Tone-His_Self-1266414414.mp3

(function (factory) {

    // checking for exports avalible
    if (typeof module !== 'undefined' && module.exports) {
        // export Collection
        module.exports = factory
    } else {
        // else add to root variable
        window['naranja'] = factory
    }

})(function () {

    function setSideUpAnimation(finalNotification) {
        setTimeout(function () {

            var notificaciónHeight = finalNotification
                .querySelector('.naranja-body-notification').offsetHeight

            finalNotification.style.height = notificaciónHeight + 'px'
        }, 0)
    }

    function createText(text) {
        return document.createTextNode(text)
    }

    /**
     * provide a reusable way to create html
     * elements
     * @param tag String – html tag name
     * @param classes Array<String> – html tag classes
     */

    function createElement(tag, classes) {
        var $HTMLElement = document.createElement(tag)
        if (!!classes) {
            classes.forEach(function (className) {
                $HTMLElement.classList.add(className)
            })
        }

        return $HTMLElement
    }

    var $narjContainer = document.querySelector('.naranja-notification-box')

    if (!$narjContainer) {
        $narjContainer = createElement('div', ['naranja-notification-box'])
        $newNotificationsAdvice = createElement('div', ['naranja-notification-advice'])
        $newNotificationsAdvice.addEventListener('click', function () {
            $narjContainer.scrollTop = '0'
        })
        $narjContainer.appendChild($newNotificationsAdvice)

        document.body.appendChild($narjContainer)
    }

    $narjContainer.__proto__.unshifElement = function (node) {
        this.insertBefore(node, this.childNodes[0])
    }

    $narjContainer.addEventListener('scroll', function (e) {
        if (e.currentTarget.scrollTop < 20) {
            $newNotificationsAdvice.classList.remove('active')
        }
    })

    return {
        log: function (argm) {
            this.createNotification('log', argm)
        }, success: function (argm) {
            this.createNotification('success', argm)
        }, warn: function (argm) {
            this.createNotification('warn', argm)
        }, error: function (argm) {
            this.createNotification('error', argm)
        },

        /*
        * Internal methods for
        * launch notifications
        */
        createNotification: function (type, argm) {

            this.type = type
            this.title = argm.title
            this.text = argm.text
            this.icon = (argm.icon === undefined) ? true : argm.icon
            this.buttons = argm.buttons

            var $notification = this.$createContainer()
            var $body = $notification.querySelector('div')

            this.$notification = $notification
            this.$body = $body

            // render icon if exists
            if (this.icon) {
                var $iconContainer = createElement('div', ['naranja-icon', 'narj-icon-' + type])

                $iconContainer.innerHTML = this.chooseIcon[type]

                $body.appendChild($iconContainer)
            }

            var $title = this.createTitle()
            var $text = this.createText()

            var $textAndTitleContainer = createElement('div', ['naranja-text-and-title'])

            $textAndTitleContainer.appendChild($title)
            $textAndTitleContainer.appendChild($text)

            $body.appendChild($textAndTitleContainer)

            // render buttons fragment if exists
            if (this.buttons) {
                var $buttons = this.createButtons($notification, $body)

                $body
                    .querySelector('.naranja-text-and-title')
                    .appendChild($buttons)
            }


            var $close = createElement('div', ['naranja-close-icon'])

            $close.addEventListener('click', (function () {
                this.closeNotification()
            }).bind(this))

            // var $close = document.createElement('div')
            // $close.classList.add('naranja-close-icon')
            $close.innerHTML = this.chooseIcon.close

            $body.appendChild($close)

            $narjContainer.unshifElement($notification)
            setSideUpAnimation($notification)

            if ($narjContainer.scrollTop > 20) {
                $newNotificationsAdvice.classList.add('active')
                $newNotificationsAdvice.innerHTML = this.chooseIcon.newNotification
            }

            if (argm.timeout !== 'keep') {
                setTimeout((function () {
                    this.closeNotification()
                }).bind(this), argm.timeout || 5000)
            }
        }, $createContainer: function () {
            // generate box for notification

            var $container = createElement('div', ['naranja-notification', 'naranja-' + this.type])

            var $innerContainer = createElement('div', ['naranja-body-notification', 'narj-' + this.type])

            $container.appendChild($innerContainer)

            return $container
        }, createTitle: function () {
            var $parragraph = createElement('p', ['naranja-title'])
            var $tt = createText(this.title)
            $parragraph.appendChild($tt)

            return $parragraph
        }, createText: function () {
            var $title = createElement('p', ['naranja-parragraph'])

            var $tx = document.createTextNode(this.text)
            $title.appendChild($tx)

            return $title
        }, createButtons: function ($notification, $body) {
            var $buttonsContainer = createElement('div', ['naranja-buttons-container'])

            var self = this

            this.buttons.forEach(function (button) {
                var $buttonElement = createElement('button')
                $buttonElement.appendChild(document.createTextNode(button.text))

                $buttonElement.addEventListener('click', function (event) {

                    self.removeNotification = true
                    event.preventClose = function () {
                        self.removeNotification = false
                    }

                    event.closeNotification = function () {
                        self.closeNotification()
                    }

                    button.click(event)

                    if (self.removeNotification) self.closeNotification()
                })

                $buttonsContainer.appendChild($buttonElement)
            })

            return $buttonsContainer
        }, closeNotification: function () {
            var self = this
            if (!this.elementWasRemoved) {
                self.$body.style.opacity = '0'
                setTimeout(function () {
                    self.$body.style.marginTop = '0px'
                    self.$body.style.marginBottom = '0px'
                    self.$body.style.padding = '0px'
                    self.$notification.style.height = 0 + 'px'
                    self.$notification.style.padding = 0 + 'px'
                    setTimeout(function () {
                        self.$notification
                            .parentNode
                            .removeChild(self.$notification)
                    }, 600);
                    if ($narjContainer.scrollTop < 20) {
                        $newNotificationsAdvice.classList.remove('active')
                    }
                }, 150)
            }
            this.elementWasRemoved = true
        }, chooseIcon: {
            log: '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="24" cy="24" r="24" fill="#EEEEEE"/><path d="M26.595 37.5C26.3313 37.9546 25.9528 38.332 25.4973 38.5943C25.0419 38.8566 24.5256 38.9947 24 38.9947C23.4744 38.9947 22.9581 38.8566 22.5027 38.5943C22.0472 38.332 21.6687 37.9546 21.405 37.5M39 31.5H9C10.1935 31.5 11.3381 31.0259 12.182 30.182C13.0259 29.3381 13.5 28.1935 13.5 27V19.5C13.5 16.7152 14.6062 14.0445 16.5754 12.0754C18.5445 10.1062 21.2152 9 24 9C26.7848 9 29.4555 10.1062 31.4246 12.0754C33.3938 14.0445 34.5 16.7152 34.5 19.5V27C34.5 28.1935 34.9741 29.3381 35.818 30.182C36.6619 31.0259 37.8065 31.5 39 31.5V31.5Z" stroke="black" stroke-opacity="0.73" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            success: '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="24" cy="24" r="24" fill="#6ED69A" fill-opacity="0.62"/><path d="M36 16.5L19.5 33L12 25.5" stroke="#11B674" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            warn: '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="24" cy="24" r="24" fill="#F5CE69"/><path d="M24 16.5C24.8285 16.5 25.5 17.1716 25.5 18V24C25.5 24.8284 24.8285 25.5 24 25.5C23.1715 25.5 22.5 24.8284 22.5 24V18C22.5 17.1716 23.1715 16.5 24 16.5Z" fill="#DCA14E"/><path d="M25.5 28.5C25.5 27.6716 24.8285 27 24 27C23.1715 27 22.5 27.6716 22.5 28.5V30C22.5 30.8284 23.1715 31.5 24 31.5C24.8285 31.5 25.5 30.8284 25.5 30V28.5Z" fill="#DCA14E"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7.5 24C7.5 14.8873 14.8873 7.5 24 7.5C33.1127 7.5 40.5 14.8873 40.5 24C40.5 33.1127 33.1127 40.5 24 40.5C14.8873 40.5 7.5 33.1127 7.5 24ZM24 10.5C16.5441 10.5 10.5 16.5442 10.5 24C10.5 31.4558 16.5441 37.5 24 37.5C31.4559 37.5 37.5 31.4558 37.5 24C37.5 16.5442 31.4559 10.5 24 10.5Z" fill="#DCA14E"/></svg>',

            error: '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="24" cy="24" r="24" fill="#ED8476"/><path d="M34.0607 16.0607C34.6465 15.4749 34.6465 14.5251 34.0607 13.9393C33.4749 13.3536 32.5251 13.3536 31.9394 13.9393L24 21.8787L16.0607 13.9393C15.4749 13.3536 14.5251 13.3536 13.9394 13.9393C13.3535 14.5251 13.3535 15.4749 13.9394 16.0607L21.8787 24L13.9394 31.9393C13.3535 32.5251 13.3535 33.4749 13.9394 34.0607C14.5251 34.6464 15.4749 34.6464 16.0607 34.0607L24 26.1213L31.9394 34.0607C32.5251 34.6464 33.4749 34.6464 34.0607 34.0607C34.6465 33.4749 34.6465 32.5251 34.0607 31.9393L26.1213 24L34.0607 16.0607Z" fill="#C54F4F"/></svg>',

            close: '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.70711 1.7071C10.0976 1.31659 10.0976 0.683407 9.70711 0.292889C9.31659 -0.0976295 8.68341 -0.0976295 8.29289 0.292889L5 3.58578L1.70711 0.292889C1.31659 -0.0976295 0.68342 -0.0976295 0.292894 0.292889C-0.0976315 0.683407 -0.0976315 1.31659 0.292894 1.7071L3.58579 5L0.292894 8.29289C-0.0976315 8.68341 -0.0976315 9.31659 0.292894 9.7071C0.68342 10.0976 1.31659 10.0976 1.70711 9.7071L5 6.41421L8.29289 9.7071C8.68341 10.0976 9.31659 10.0976 9.70711 9.7071C10.0976 9.31659 10.0976 8.68341 9.70711 8.29289L6.41422 5L9.70711 1.7071Z" fill="black" fill-opacity="0.37"/></svg>',

            newNotification: '<svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#filter0_d)"><circle cx="20.5" cy="16.5" r="16.5" fill="#C08AE1"/></g><path d="M13 21L21 13L29 21" stroke="black" stroke-opacity="0.6" stroke-width="3"/><defs><filter id="filter0_d" x="0" y="0" width="41" height="41" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feOffset dy="4"/><feGaussianBlur stdDeviation="2"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter></defs></svg>'
        }
    }
})
