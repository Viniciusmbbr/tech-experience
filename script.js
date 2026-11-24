const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#site-nav');

if (menuButton && navigation) {
    const closeMenu = () => {
        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.setAttribute('aria-label', 'Abrir menu');
        navigation.classList.remove('is-open');
    };

    menuButton.addEventListener('click', () => {
        const isOpen = menuButton.getAttribute('aria-expanded') === 'true';
        menuButton.setAttribute('aria-expanded', String(!isOpen));
        menuButton.setAttribute('aria-label', isOpen ? 'Abrir menu' : 'Fechar menu');
        navigation.classList.toggle('is-open', !isOpen);
    });

    navigation.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('click', (event) => {
        if (menuButton.getAttribute('aria-expanded') === 'true'
            && !menuButton.contains(event.target)
            && !navigation.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menuButton.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            menuButton.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 700) closeMenu();
    });
}

const message = document.querySelector('#mensagem');
const characterCounter = document.querySelector('#contador-mensagem');

if (message && characterCounter) {
    const updateCounter = () => {
        const remaining = Math.max(0, 200 - message.value.length);
        const word = remaining === 1 ? 'caractere' : 'caracteres';
        characterCounter.textContent = `Restam ${remaining} ${word}.`;
    };

    message.addEventListener('input', updateCounter);
    updateCounter();
}

const registrationForm = document.querySelector('.registration-form');
const workshopGroup = document.querySelector('#oficinas-grupo');
const workshopError = document.querySelector('#oficinas-erro');

if (registrationForm && workshopGroup && workshopError) {
    const workshops = [...registrationForm.querySelectorAll('input[name="oficinas[]"]')];
    const clearWorkshopError = () => {
        workshopGroup.classList.remove('is-invalid');
        workshopError.textContent = '';
        workshops.forEach((workshop) => workshop.removeAttribute('aria-invalid'));
    };

    workshops.forEach((workshop) => {
        workshop.addEventListener('change', () => {
            if (workshops.some((item) => item.checked)) clearWorkshopError();
        });
    });

    document.querySelectorAll('[data-oficina]').forEach((link) => {
        link.addEventListener('click', () => {
            const workshop = workshops.find((item) => item.value === link.dataset.oficina);
            if (workshop) {
                workshop.checked = true;
                clearWorkshopError();
            }
        });
    });

    const submitButton = registrationForm.querySelector('button[type="submit"]');
    const buttonContent = submitButton ? [...submitButton.childNodes].map((node) => node.cloneNode(true)) : [];
    window.addEventListener('pageshow', () => {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.replaceChildren(...buttonContent.map((node) => node.cloneNode(true)));
        }
    });

    registrationForm.addEventListener('submit', (event) => {
        if (!workshops.some((workshop) => workshop.checked)) {
            event.preventDefault();
            workshopGroup.classList.add('is-invalid');
            workshopError.textContent = 'Selecione pelo menos uma oficina para continuar.';
            workshops.forEach((workshop) => workshop.setAttribute('aria-invalid', 'true'));
            workshops[0]?.focus();
            return;
        }

        clearWorkshopError();
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Processando inscrição…';
        }
    });
}
