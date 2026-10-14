const navToggle = document.querySelector('[data-nav-toggle]');
const navbar = document.querySelector('.navbar');

if (navToggle && navbar) {
    navToggle.addEventListener('click', () => {
        navbar.classList.toggle('nav-open');
    });
}

document.querySelectorAll('input[type="date"]').forEach((input) => {
    if (!input.min) {
        input.min = new Date().toISOString().slice(0, 10);
    }
});

const plannerForm = document.querySelector('[data-planner-form]');
const totalPreview = document.querySelector('[data-total-preview]');

if (plannerForm && totalPreview) {
    const updatePreview = () => {
        const packagePrice = Number(plannerForm.querySelector('[name="package_price"]')?.value || 0);
        const travelers = Math.max(1, Number(plannerForm.querySelector('[name="travelers"]')?.value || 1));
        const start = plannerForm.querySelector('[name="start_date"]')?.value;
        const end = plannerForm.querySelector('[name="end_date"]')?.value;
        const accommodation = Number(plannerForm.querySelector('[name="accommodation_id"]')?.selectedOptions?.[0]?.dataset.price || 0);
        const vehicle = Number(plannerForm.querySelector('[name="vehicle_id"]')?.selectedOptions?.[0]?.dataset.price || 0);
        const guide = Number(plannerForm.querySelector('[name="guide_id"]')?.selectedOptions?.[0]?.dataset.price || 0);
        let days = 1;

        if (start && end) {
            const ms = new Date(end) - new Date(start);
            days = Math.max(1, Math.ceil(ms / 86400000));
        }

        const total = (packagePrice * travelers) + ((accommodation + vehicle + guide) * days);
        totalPreview.textContent = `$${total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    plannerForm.addEventListener('input', updatePreview);
    updatePreview();
}
