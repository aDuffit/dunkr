import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import ChartDataLabels from 'chartjs-plugin-datalabels';
import {Chart, registerables} from 'chart.js';

// On enregistre le plugin globalement
Chart.register(...registerables, ChartDataLabels);
