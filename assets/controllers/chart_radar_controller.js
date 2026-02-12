// assets/controllers/chart_radar_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('chartjs:pre-connect', (event) => {
            const config = event.detail.config;

            if (!config.plugins) {
                config.plugins = [];
            }

            const radialLabelsPlugin = {
                id: 'customRadialLabels',
                afterDraw: (chart) => {
                    const { ctx, scales: { r } } = chart;
                    if (!r || !r.getLabels) return;

                    const xCenter = r.xCenter;
                    const yCenter = r.yCenter;
                    const labels = r.getLabels();

                    // On réduit légèrement le rayon de dessin du graphique
                    // pour laisser de la place aux labels à l'intérieur du canvas
                    const labelRadius = r.drawingArea + 25;

                    ctx.save();
                    // On utilise la police système pour plus de clarté
                    ctx.font = 'bold 10px sans-serif';
                    // Couleur adaptative DaisyUI (bc = base content)
                    ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--bc').trim() || '#666';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    labels.forEach((label, i) => {
                        // Calcul de l'angle précis du segment
                        const angle = r.getIndexAngle(i) - Math.PI / 2;

                        ctx.save();
                        // Positionnement du point d'ancrage du texte
                        ctx.translate(
                            xCenter + Math.cos(angle) * labelRadius,
                            yCenter + Math.sin(angle) * labelRadius
                        );

                        // Rotation perpendiculaire au rayon
                        let rotation = angle + Math.PI / 2;

                        // Inversion du texte pour la partie basse (lisibilité)
                        if (rotation > Math.PI / 2 && rotation < (3 * Math.PI) / 2) {
                            rotation -= Math.PI;
                        }

                        ctx.rotate(rotation);

                        // Dessin du texte
                        ctx.fillText(label, 0, 0);
                        ctx.restore();
                    });
                    ctx.restore();
                }
            };

            config.plugins.push(radialLabelsPlugin);

            // Désactivation des labels natifs pour éviter les conflits
            config.options.scales.r.pointLabels = { display: false };

            // On force un padding global pour que les labels ne soient pas coupés
            if (!config.options.layout) config.options.layout = {};
            config.options.layout.padding = 60;
        });
    }
}