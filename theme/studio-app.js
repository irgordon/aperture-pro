/* global wp, ApertureProStudio */

(function () {
    const api = wp.apiFetch;
    const rest = ApertureProStudio.restUrl;

    function qs(selector, root = document) {
        return (root || document).querySelector(selector);
    }

    function createProjectCard(project) {
        const div = document.createElement('div');
        div.className = 'ap-project-card';

        const img = document.createElement('img');
        img.className = 'ap-project-thumb';
        img.src = project.thumbnail || '';
        img.alt = project.title || '';

        const body = document.createElement('div');
        body.className = 'ap-project-body';

        const title = document.createElement('div');
        title.className = 'ap-project-title';
        title.textContent = project.title || 'Untitled Project';

        const meta = document.createElement('div');
        meta.className = 'ap-project-meta';
        meta.textContent = project.subtitle || project.client || '';

        const tag = document.createElement('span');
        tag.className = 'ap-project-tag';
        tag.textContent = project.tag || 'Featured';

        body.appendChild(title);
        if (meta.textContent) body.appendChild(meta);
        body.appendChild(tag);

        if (project.thumbnail) {
            div.appendChild(img);
        }
        div.appendChild(body);

        return div;
    }

    async function loadFeaturedProjects() {
        const container = qs('#ap-studio-featured-projects');
        if (!container) return;

        container.innerHTML = '';

        const skeleton = document.createElement('div');
        skeleton.className = 'ap-project-card';
        skeleton.style.opacity = '0.4';
        skeleton.innerHTML = '<div class="ap-project-body">Loading projects…</div>';
        container.appendChild(skeleton);

        try {
            const data = await api({
                path: 'aperture-pro/v1/projects?featured=1',
                method: 'GET',
            });

            container.innerHTML = '';

            if (!data || !data.projects || !data.projects.length) {
                container.innerHTML = '<div class="ap-project-body">No featured projects yet.</div>';
                return;
            }

            data.projects.forEach((project) => {
                container.appendChild(createProjectCard(project));
            });
        } catch (e) {
            console.error('Failed to load featured projects', e);
            container.innerHTML = '<div class="ap-project-body">Unable to load projects right now.</div>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadFeaturedProjects();
    });
})();
